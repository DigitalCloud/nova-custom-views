<?php

namespace devmtm\NovaCustomViews;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Laravel\Nova\Console\Concerns\AcceptsNameAndVendor;
use Laravel\Nova\Nova;
use Symfony\Component\Process\Process;

class ViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nova:view {resource} {view}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new view for a specific resource';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if(!is_dir($this->viewsBasePath())) {
            mkdir($this->viewsBasePath());
        }

        (new Filesystem)->copyDirectory(
            __DIR__ . '/stubs/view',
            $this->viewPath()
        );

        // View.js replacements...
        $this->replace('{{ viewClass }}', $this->viewClass(), $this->viewPath().'/resources/js/views/View.vue');
        (new Filesystem)->move(
            $this->viewPath().'/resources/js/views/View.vue',
            $this->viewPath().'/resources/js/views/'.$this->viewClass().'.vue'
        );

        // Views.js replacements...
        $this->replace('{{ component }}', $this->component(), $this->viewPath().'/resources/js/views.js');
        $this->replace('{{ viewClass }}', $this->viewClass(), $this->viewPath().'/resources/js/views.js');

        // ViewsServiceProvider.php replacements...
        $this->replace('{{ resourceClass }}', $this->resourceClass(), $this->viewPath().'/src/ViewServiceProvider.stub');
        $this->replace('{{ viewClass }}', $this->viewClass(), $this->viewPath().'/src/ViewServiceProvider.stub');
        $this->replace('{{ namespace }}', $this->namespace(), $this->viewPath().'/src/ViewServiceProvider.stub');
        $this->replace('{{ component }}', $this->component(), $this->viewPath().'/src/ViewServiceProvider.stub');
        $this->replace('{{ resource }}', $this->resourceName(), $this->viewPath().'/src/ViewServiceProvider.stub');
        $this->replace('{{ view }}', $this->viewName(), $this->viewPath().'/src/ViewServiceProvider.stub');

        (new Filesystem)->move(
            $this->viewPath().'/src/ViewServiceProvider.stub',
            $this->viewPath().'/src/'.$this->resourceClass().$this->viewClass().'ViewServiceProvider.php'
        );

        // Views composer.json replacements...
        $this->replace('{{ name }}', $this->composerName(), $this->viewPath().'/composer.json');
        $this->replace('{{ escapedNamespace }}', $this->escapedNamespace(), $this->viewPath().'/composer.json');
        $this->replace('{{ resourceClass }}', $this->resourceClass(), $this->viewPath().'/composer.json');
        $this->replace('{{ viewClass }}', $this->viewClass(), $this->viewPath().'/composer.json');

        // Register the views...
        $this->addViewsRepositoryToRootComposer();
        $this->addViewsPackageToRootComposer();
        $this->addScriptsToNpmPackage();

        if ($this->confirm("Would you like to install the views's NPM dependencies?", true)) {
            $this->installNpmDependencies();

            $this->output->newLine();
        }

        if ($this->confirm("Would you like to compile the views's assets?", true)) {
            $this->compile();

            $this->output->newLine();
        }

        if ($this->confirm('Would you like to update your Composer packages?', true)) {
            $this->composerUpdate();
        }
    }

    /**
     * Add a path repository for the views to the application's composer.json file.
     *
     * @return void
     */
    protected function addViewsRepositoryToRootComposer()
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        $composer['repositories'][] = [
            'type' => 'path',
            'url' => './'.$this->relativeViewPath(),
        ];

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Add a package entry for the views to the application's composer.json file.
     *
     * @return void
     */
    protected function addViewsPackageToRootComposer()
    {
        $composer = json_decode(file_get_contents(base_path('composer.json')), true);

        $composer['require'][$this->composerName()] = '*';

        file_put_contents(
            base_path('composer.json'),
            json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Add a path repository for the views to the application's composer.json file.
     *
     * @return void
     */
    protected function addScriptsToNpmPackage()
    {
        $package = json_decode(file_get_contents(base_path('package.json')), true);

        $package['scripts']['build-'.$this->resourceName() . '-' . $this->viewName()] = 'cd '.$this->relativeViewPath().' && npm run dev';
        $package['scripts']['build-'.$this->resourceName() . '-' . $this->viewName().'-prod'] = 'cd '.$this->relativeViewPath().' && npm run prod';

        file_put_contents(
            base_path('package.json'),
            json_encode($package, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Install the views's NPM dependencies.
     *
     * @return void
     */
    protected function installNpmDependencies()
    {
        $this->runCommand('npm set progress=false && npm install', $this->viewPath());
    }

    /**
     * Compile the views's assets.
     *
     * @return void
     */
    protected function compile()
    {
        $this->runCommand('npm run dev', $this->viewPath());
    }

    /**
     * Update the project's composer dependencies.
     *
     * @return void
     */
    protected function composerUpdate()
    {
        $this->runCommand('composer update', getcwd());
    }

    /**
     * Run the given command as a process.
     *
     * @param  string  $command
     * @param  string  $path
     * @return void
     */
    protected function runCommand($command, $path)
    {
        $process = (new Process($command, $path))->setTimeout(null);

        if ('\\' !== DIRECTORY_SEPARATOR && file_exists('/dev/tty') && is_readable('/dev/tty')) {
            $process->setTty(true);
        }

        $process->run(function ($type, $line) {
            $this->output->write($line);
        });
    }

    /**
     * Replace the given string in the given file.
     *
     * @param  string  $search
     * @param  string  $replace
     * @param  string  $path
     * @return void
     */
    protected function replace($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }

    /**
     * Get the path to the tool.
     *
     * @return string
     */
    protected function viewsBasePath()
    {
        return base_path('nova-components/views/');
    }

    /**
     * Get the path to the tool.
     *
     * @return string
     */
    protected function viewPath()
    {
        return base_path($this->relativeViewPath());
    }

    /**
     * Get the relative path to the views.
     *
     * @return string
     */
    protected function relativeViewPath()
    {
        return 'nova-components/views/' . $this->resourceName() . '/' . $this->viewName();
    }

    protected function namespace()
    {
        return 'NovaCustomViews\\' . $this->resourceClass() . $this->ViewClass() . 'View';
    }

    /**
     * Get the views's escaped namespace.
     *
     * @return string
     */
    protected function escapedNamespace()
    {
        return str_replace('\\', '\\\\', $this->namespace());
    }

    protected function component()
    {
        return $this->resourceName() . '-' . $this->viewName() . '-view';
    }


    public function composerName()
    {
        return 'nova-custom-views/' . $this->component();
    }

    protected function resourceName()
    {
        return $this->argument('resource');
    }

    protected function resourceClass()
    {
        return Str::studly($this->resourceName());
    }

    protected function viewName()
    {
        return strtolower($this->argument('view'));
    }

    protected function viewClass()
    {
        return Str::studly($this->viewName());
    }

}
