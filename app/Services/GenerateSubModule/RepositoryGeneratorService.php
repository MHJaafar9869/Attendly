<?php

declare(strict_types=1);

namespace App\Services\GenerateSubModule;

use Illuminate\Support\Facades\File;

class RepositoryGeneratorService
{
    /**
     * Generate a repository and its interface for the given module and model.
     *
     * @param  string  $module  The module name (e.g. "Blog").
     * @param  string  $model  The model name (e.g. "Post").
     * @return string[]
     */
    public static function generate(string $module, string $model)
    {
        // Define repository directory and file paths
        $repositoryDir = module_path($module, "app/Repositories/{$model}");
        $repositoryPath = $repositoryDir."/{$model}Repository.php";
        $interfacePath = $repositoryDir."/{$model}RepositoryInterface.php";

        // Ensure directory exists
        if (! File::isDirectory($repositoryDir)) {
            File::makeDirectory($repositoryDir, 0755, true);
        }

        // --- Repository File ---
        if (File::exists(path: $repositoryPath)) {
            return [
                'status' => 'exists',
                'message' => "ℹ️  {$model}Repository already exists in {$repositoryDir}",
            ];
        }

        $repositoryStub = "<?php

declare(strict_types=1);

namespace Modules\\{$module}\\Repositories\\{$model};

use Modules\\{$module}\\Repositories\\{$model}\\{$model}RepositoryInterface;
use App\Repositories\BaseRepository\BaseRepository;
use Modules\\{$module}\\Models\\{$model};

class {$model}Repository extends BaseRepository implements {$model}RepositoryInterface
{
    public function __construct({$model} \$model)
    {
        parent::__construct(\$model);
    }
}
";
        File::put($repositoryPath, $repositoryStub);

        // --- Repository Interface File ---
        if (File::exists($interfacePath)) {
            return [
                'status' => 'exists',
                'message' => "ℹ️  {$model}RepositoryInterface already exists in {$repositoryDir}.",
            ];
        }

        $interfaceStub = "<?php

declare(strict_types=1);

namespace Modules\\{$module}\\Repositories\\{$model};

use App\Repositories\BaseRepository\BaseRepositoryInterface;

interface {$model}RepositoryInterface extends BaseRepositoryInterface
{
    //
}
";
        File::put($interfacePath, $interfaceStub);

        return [
            'status' => 'success',
            'message' => "✅ {$model}Repository and {$model}RepositoryInterface created successfully in {$repositoryDir}.",
        ];
    }
}
