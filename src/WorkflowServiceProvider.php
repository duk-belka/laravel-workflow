<?php

namespace Brexis\LaravelWorkflow;

use Illuminate\Support\ServiceProvider;
use Brexis\LaravelWorkflow\Commands\WorkflowDumpCommand;

/**
 * @author Boris Koumondji <brexis@yahoo.fr>
 */
final class WorkflowServiceProvider extends ServiceProvider
{
    protected $commands = [
        WorkflowDumpCommand::class,
    ];

    /**
     * Bootstrap the application services...
     *
     * @return void
     */
    public function boot()
    {
        $configPath = $this->configPath();

        $this->publishes(
            [$configPath => config_path('workflow.php')], 'config'
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->configPath(), 'workflow');

        $this->commands($this->commands);

        $this->app->singleton(
            WorkflowLibrarianInterface::class,
            function ($app) {
                return new WorkflowLibrarian($app['config']->get('workflow'));
            }
        );
    }

    protected function configPath()
    {
        return __DIR__ . '/../config/workflow.php';
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [WorkflowLibrarianInterface::class];
    }
}
