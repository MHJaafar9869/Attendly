<?php

namespace App\Console\Commands;

use App\Services\GenerateSubModule\ApiRouteService;
use App\Services\GenerateSubModule\ColumnSyncService;
use App\Services\GenerateSubModule\ControllerGeneratorService;
use App\Services\GenerateSubModule\ModelSeederService;
use App\Services\GenerateSubModule\ProviderBindService;
use App\Services\GenerateSubModule\RepositoryGeneratorService;
use App\Services\GenerateSubModule\RequestGeneratorService;
use App\Services\GenerateSubModule\ResourceGeneratorService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class GenerateSubModule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * Usage:
     *   php artisan make:crud User Core --only=controller,resource
     *   php artisan make:crud User Core --except=seeder,routes
     *
     * @var string
     */
    protected $signature = 'make:crud {models : Comma-separated list of model names} {module}
                        {--only= : Comma-separated list of generators to run (e.g. controller,resource)}
                        {--except= : Comma-separated list of generators to skip (e.g. seeder,routes)}
                        {--soft_deletes= : Model with SoftDeletes}';

    /**
     * Command description shown in "php artisan list".
     *
     * @var string
     */
    protected $description = 'Generate CRUD (Controller, Requests, Resource, Repository, Routes, Seeder, Bindings) inside an HMVC Module';

    /**
     * Map of services and their short keys for --only / --except.
     */
    protected array $serviceMap = [
        'routes' => ApiRouteService::class,
        'seeder' => ModelSeederService::class,
        'columns' => ColumnSyncService::class,
        'provider' => ProviderBindService::class,
        'request' => RequestGeneratorService::class,
        'resource' => ResourceGeneratorService::class,
        'repository' => RepositoryGeneratorService::class,
        'controller' => ControllerGeneratorService::class,
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = $this->argument('module'); // e.g. Core
        $modelsInput = $this->argument('models'); // e.g. "User,Post,Comment"
        $messages = [];

        $models = $models = str_contains($modelsInput, ',') !== false
            ? array_map('trim', explode(',', rtrim($modelsInput, ',')))
            : [$modelsInput];

        $only = $this->option('only') ? array_map('trim', explode(',', rtrim($this->option('only'), ','))) : [];
        $except = $this->option('except') ? array_map('trim', explode(',', rtrim($this->option('except'), ','))) : [];
        $softDeletes = $this->option('soft_deletes') && in_array($this->option('soft_deletes'), [true, false]) ? $this->option('soft_deletes') : false;

        // Determine which services to run
        $services = $this->serviceMap;

        if (! empty($only)) {
            $invalid = array_diff($only, array_keys($this->serviceMap));
            if (! empty($invalid)) {
                $this->error('Invalid --only options: '.implode(', ', $invalid));

                return SymfonyCommand::FAILURE;
            }

            $services = array_filter(
                $services,
                fn ($key) => in_array($key, $only),
                ARRAY_FILTER_USE_KEY
            );
        }

        if (! empty($except)) {
            $invalid = array_diff($except, array_keys($this->serviceMap));
            if (! empty($invalid)) {
                $this->error('Invalid --except options: '.implode(', ', $invalid));

                return SymfonyCommand::FAILURE;
            }

            $services = array_filter(
                $services,
                fn ($key) => ! in_array($key, $except),
                ARRAY_FILTER_USE_KEY
            );
        }

        // Loop through each model
        foreach ($models as $model) {
            foreach ($services as $key => $service) {
                try {
                    $messages[] = $service::generate($module, $model);
                } catch (\Exception $e) {
                    $messages[] = [
                        'status' => 'failed',
                        'message' => "Exception in {$key} for model {$model}: ".$e->getMessage(),
                    ];
                }
            }
        }

        // Display results
        foreach ($messages as $msg) {
            switch ($msg['status']) {
                case 'success':
                    $this->info($msg['message']);
                    break;
                case 'exists':
                    $this->line($msg['message']);
                    break;
                case 'failed':
                    $this->error($msg['message']);
                    break;
            }
        }

        Artisan::call('optimize');
    }
}
