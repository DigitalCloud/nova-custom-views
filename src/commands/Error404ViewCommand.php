<?php

namespace devmtm\NovaCustomViews;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Laravel\Nova\Console\Concerns\AcceptsNameAndVendor;
use Laravel\Nova\Nova;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class Error404ViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nova:404';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new error 404 view';

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
            __DIR__ . '/../stubs/404',
            $this->viewsPath()
        );

        (new Filesystem)->move(
            $this->viewsPath().'/src/Custom404ServiceProvider.stub',
            $this->viewsPath().'/src/Custom404ServiceProvider.php'
        );

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
            'url' => './'.$this->relativeViewsPath(),
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

        $composer['require']['nova-custom-views/custom-404'] = '*';

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

        $package['scripts']['build-custom-404'] = 'cd '.$this->relativeViewsPath().' && npm run dev';
        $package['scripts']['build-custom-404'.'-prod'] = 'cd '.$this->relativeViewsPath().' && npm run prod';

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
        $this->runCommand('npm set progress=false && npm install', $this->viewsPath(), $this->output);
    }

    /**
     * Compile the views's assets.
     *
     * @return void
     */
    protected function compile()
    {
        $this->runCommand('npm run dev', $this->viewsPath(), $this->output);
    }

    /**
     * Update the project's composer dependencies.
     *
     * @return void
     */
    protected function composerUpdate()
    {
        $this->runCommand('composer update', getcwd(), $this->output);
    }

    /**
     * Run the given command as a process.
     *
     * @param  string  $command
     * @param  string  $path
     * @return void
     */
    protected function runCommand($command, $path, OutputInterface $output)
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
    protected function viewsPath()
    {
        return base_path('nova-components/views/404');
    }

    /**
     * Get the relative path to the views.
     *
     * @return string
     */
    protected function relativeViewsPath()
    {
        return 'nova-components/views/404';
    }

}
