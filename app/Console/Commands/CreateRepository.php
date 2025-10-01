<?php

namespace App\Console\Commands;

use App\Services\GenerateSubModule\RepositoryGeneratorService;
use Illuminate\Console\Command;

class CreateRepository extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository|make:repo {model} {module}';

    /**
     * The command aliases.
     *
     * @var array
     */
    protected $aliases = ['make:repo'];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a repository and interface in a module (HMVC pattern)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $module = $this->argument('module'); // e.g. Core
        $model = $this->argument('model');   // e.g. User

        $result = RepositoryGeneratorService::generate($module, $model);

        switch ($result['status']) {
            case 'success':
                $this->info($result['message']);
                break;
            case 'exists':
                $this->line($result['message']);
                break;
            case 'failed':
                $this->error($result['message']);
                break;
        }
    }
}
