<?php

namespace devmtm\NovaCustomViews;

use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Laravel\Nova\Console\Concerns\AcceptsNameAndVendor;
use Laravel\Nova\Nova;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

class ViewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nova:views {name} {view?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new views for a specific resource';


    private $views = [
        'index' => ['route' => 'index', 'component' => 'Index'],
        'lens' => ['route' => 'lens', 'component' => 'Lens'],
        'detail' => ['route' => 'detail', 'component' => 'Detail'],
        'create' => ['route' => 'create', 'component' => 'Create'],
        'update' => ['route' => 'edit', 'component' => 'Update'],
        'update-attached' => ['route' => 'edit-attached', 'component' => 'UpdateAttached'],
        'attach' => ['route' => 'attach', 'component' => 'Attach']
    ];
    //private $views = ['index', 'lens', 'detail', 'create', 'update', 'update-attached', 'attach'];
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if($this->viewName() && !array_key_exists($this->viewName(), $this->views)) {
            return $this->error('Invalid view name: ' . $this->viewClass());
        }
        if(!is_dir($this->viewsBasePath())) {
            mkdir($this->viewsBasePath());
        }

        if(!is_dir($this->viewsBasePath() . '/' . $this->resourceName())) {
            $this->handelNewResource();
        } else {
            $this->handelExistedResource();

        }

        $this->handleServiceProvider();

        $this->handleComponentRegistrar();

        if ($this->confirm("Would you like to compile the views's assets?", true)) {
            $this->compile();

            $this->output->newLine();
        }


    }

    protected function handelNewResource() {

        (new Filesystem)->copyDirectory(
            __DIR__ . '/../stubs/views',
            $this->viewsPath()
        );

        if($this->viewName()) {
            (new Filesystem)->deleteDirectory(
                $this->viewsPath() . '/resources/js/views/'
            );
            (new Filesystem)->makeDirectory(
                $this->viewsPath() . '/resources/js/views/'
            );
            $this->copyView($this->novaView());
            $this->addViewToConfig($this->viewClass());
        } else {
            $this->addAllViewsToConfig();
        }

        $this->handleComposer();
        $this->addViewsRepositoryToRootComposer();
        $this->addViewsPackageToRootComposer();
        $this->addScriptsToNpmPackage();

        if ($this->confirm("Would you like to install the views's NPM dependencies?", true)) {
            $this->installNpmDependencies();

            $this->output->newLine();
        }

        if ($this->confirm('Would you like to update your Composer packages?', true)) {
            $this->composerUpdate();
        }
    }

    protected function handelExistedResource() {
        if($this->viewName()) {
            $this->copyView($this->novaView());
            $this->addViewToConfig($this->viewClass());
        } else {
            $this->copyAllViews();
            $this->addAllViewsToConfig();
            // handle all views if already existed
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

        $package['scripts']['build-'.$this->viewsName()] = 'cd '.$this->relativeViewsPath().' && npm run dev';
        $package['scripts']['build-'.$this->viewsName().'-prod'] = 'cd '.$this->relativeViewsPath().' && npm run prod';

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

    protected function handleServiceProvider() {
        $this->copyServiceProvider();
        $this->replace('{{ resourceClass }}', $this->resourceClass(), $this->viewsPath().'/src/ViewsServiceProvider.stub');
        $this->replace('{{ namespace }}', $this->namespace(), $this->viewsPath().'/src/ViewsServiceProvider.stub');
        $this->replace('{{ resource }}', $this->resourceName(), $this->viewsPath().'/src/ViewsServiceProvider.stub');
        $this->replace('{{ registeredViews }}', $this->registeredViews(), $this->viewsPath().'/src/ViewsServiceProvider.stub');
        $this->publishServiceProvider();
    }

    protected function handleComponentRegistrar() {
        $this->copyComponentsRegistrar();
        $this->replace('{{ registeredViews }}', $this->registeredViews(), $this->viewsPath().'/resources/js/views.js');
    }

    protected function handleComposer() {
        //$this->copyComposer();
        $this->replace('{{ name }}', $this->composerName(), $this->viewsPath().'/composer.json');
        $this->replace('{{ escapedNamespace }}', $this->escapedNamespace(), $this->viewsPath().'/composer.json');
        $this->replace('{{ class }}', $this->resourceClass(), $this->viewsPath().'/composer.json');

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
        return base_path('nova-components/views/' . $this->resourceName());
    }

    /**
     * Get the relative path to the views.
     *
     * @return string
     */
    protected function relativeViewsPath()
    {
        return 'nova-components/views/' . $this->viewsName();
    }

    /**
     * Get the namespace.
     *
     * @return string
     */
    protected function namespace()
    {
        return 'NovaCustomViews\\' . $this->resourceClass() . 'Views';
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

    /**
     * Get the resource's class name.
     *
     * @return string
     */
    protected function resourceClass()
    {
        return Str::studly($this->viewsName());
    }

    /**
     * Get the resource's base name.
     *
     * @return string
     */
    protected function viewsName()
    {
        return $this->argument('name');
    }

    protected function resourceName()
    {
        return strtolower($this->argument('name'));
    }

    protected function component()
    {
        return $this->resourceName() . '-views';
    }


    public function composerName()
    {
        return 'nova-custom-views/' . $this->viewsName() . '-views';
    }


    protected function viewName() {
        return strtolower($this->argument('view'));
    }

    protected function viewClass()
    {
        return Str::studly($this->viewName());
    }

    protected function viewDetails() {
        return $this->views[$this->viewName()];
    }

    protected function novaView()
    {
        return $this->viewClass() . '.vue';
    }

    protected function copyView($view) {
        (new Filesystem)->copy(
            __DIR__ . '/../stubs/views/resources/js/views/' . $view,
            $this->viewsPath() . '/resources/js/views/' . $view
        );
    }

    protected function copyAllViews() {
        (new Filesystem)->copyDirectory(
            __DIR__ . '/../stubs/views/resources/js/views',
            $this->viewsPath() . '/resources/js/views'
        );
    }

    protected function copyServiceProvider() {
        (new Filesystem)->copy(
            __DIR__ . '/../stubs/views/src/ViewsServiceProvider.stub',
            $this->viewsPath() . '/src/ViewsServiceProvider.stub'
        );
    }

    protected function publishServiceProvider() {
        (new Filesystem)->move(
            $this->viewsPath() . '/src/ViewsServiceProvider.stub',
            $this->viewsPath() . '/src/' . $this->resourceClass() . 'ViewsServiceProvider.php'
        );
    }

    protected function copyComponentsRegistrar() {
        (new Filesystem)->copy(
            __DIR__ . '/../stubs/views/resources/js/views.js',
            $this->viewsPath() . '/resources/js/views.js'
        );
    }

    protected function copyComposer() {
        (new Filesystem)->copy(
            __DIR__ . '/../stubs/views/composer.json',
            $this->viewsPath() . '/composer.json'
        );
    }

    protected function addViewToConfig()
    {
        $config = json_decode(file_get_contents($this->viewsPath() . '/config.json'), true);

        $config['views'][$this->resourceName()][$this->viewDetails()['route']] = array_merge($this->viewDetails(), ['name' => $this->resourceName() . '-' . $this->viewName() . '-view']);

        file_put_contents(
            $this->viewsPath() . '/config.json',
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    protected function addAllViewsToConfig()
    {
        $config = json_decode(file_get_contents($this->viewsPath() . '/config.json'), true);

        foreach($this->views as $view => $viewDetails) {
            $config['views'][$this->resourceName()][$viewDetails['route']] = array_merge($viewDetails, ['name' => $this->resourceName() . '-' . $viewDetails['route'] . '-view']);
        }
        file_put_contents(
            $this->viewsPath() . '/config.json',
            json_encode($config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    protected function registeredViews() {
        $config = json_decode(file_get_contents($this->viewsPath() . '/config.json'), true);
        $views = isset($config['views'][$this->resourceName()])? $config['views'][$this->resourceName()] : [];
        $registeredViews = [];
        foreach ($views as $view => $component) {
            $registeredViews[$view] = $component;
        }
        return json_encode($registeredViews);
    }
}
