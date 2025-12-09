<?php

namespace Modules\Core\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class MakeModuleService extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:module-service
                           {module : Module name}
                           {service : Service class name (with subfolders)}
                           {--force : Overwrite existing service file}';

    /**
     * The console command description.
     */
    protected $description = 'Generate a service file inside a module with proper namespace';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $module = $this->argument('module');
        $name = $this->argument('service');

        $force = $this->option('force');

        $normalized = str_replace('\\', '/', $name);

        $className = basename($normalized);

        $subDir = trim(dirname($normalized), '.');

        $relativeDir = "Modules/{$module}/Services".($subDir !== '.' ? "/{$subDir}" : '');
        $relativePath = "{$relativeDir}/{$className}.php";
        $fullPath = base_path($relativePath);

        if (! is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0777, true);
        }

        if (file_exists($fullPath) && ! $force) {
            $this->error("Service already exists: {$relativePath} (use --force to overwrite)");

            return SymfonyCommand::FAILURE;
        }

        $namespaceParts = [
            'Modules',
            $module,
            'Services',
        ];

        if ($subDir && $subDir !== '.') {
            foreach (explode('/', $subDir) as $part) {
                $namespaceParts[] = $part;
            }
        }

        $namespace = implode('\\', $namespaceParts);

        $content = <<<PHP
<?php

declare(strict_types=1);

namespace {$namespace};

final readonly class {$className}
{
    public function __construct(
        // ...
    ) {}
}

PHP;

        file_put_contents($fullPath, $content);
        $relativePath = str_replace('//', '/', $relativePath);
        $this->info("[Service] {$relativePath}");

        return SymfonyCommand::SUCCESS;
    }
}
