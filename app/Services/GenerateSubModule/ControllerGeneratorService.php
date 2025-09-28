<?php

declare(strict_types=1);

namespace App\Services\GenerateSubModule;

use Illuminate\Support\Facades\File;

class ControllerGeneratorService
{
    /**
     * Generate an API controller for a given module and model.
     *
     * @param  string  $module  The module name (e.g. "Blog").
     * @param  string  $model  The model name (e.g. "Post").
     * @return string[]
     */
    public static function generate(string $module, string $model, bool $softDeletes = false): array
    {
        // Build the directory and file path for the controller
        $controllerDir = module_path($module, 'app/Http/Controllers/Api');
        $controllerPath = $controllerDir."/{$model}Controller.php";

        // Lowercase version of the model (used in variable names)
        $modelSmallCase = strtolower($model);

        // Namespaces for required dependencies
        $namespaceRepo = "Modules\\{$module}\\Repositories\\{$model}\\{$model}RepositoryInterface";
        $namespaceResource = "Modules\\{$module}\\Transformers\\{$model}\\{$model}Resource";
        $storeRequestClass = "Modules\\{$module}\\Http\\Requests\\{$model}\\Store{$model}Request";
        $updateRequestClass = "Modules\\{$module}\\Http\\Requests\\{$model}\\Update{$model}Request";

        // Ensure the controller directory exists, create if missing
        if (! File::isDirectory($controllerDir)) {
            File::makeDirectory($controllerDir, 0755, true);
        }

        // If the controller file already exists, throw exception
        if (File::exists($controllerPath)) {
            return [
                'status' => 'exists',
                'message' => "ℹ️  {$model}Controller already exists in {$controllerDir}.",
            ];
        }
        $softDeleteMethods = '';
        if ($softDeletes) {
            $softDeleteMethods = "
    public function restore(int|string \$id): JsonResponse
    {
        \$this->{$modelSmallCase}Repo->restore(\$id);
        return \$this->respondSuccess('{$model} restored successfully');
    }

    public function forceDelete(int|string \$id): JsonResponse
    {
        \$this->{$modelSmallCase}Repo->forceDelete(\$id);
        return \$this->respondSuccess('{$model} permanently deleted successfully');
    }";
        }

        // Controller stub template with basic CRUD methods
        $controllerStub = "<?php

declare(strict_types=1);

namespace Modules\\{$module}\\Http\\Controllers\\Api\\{$model};

use {$namespaceRepo};
use App\Traits\ResponseJson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use {$storeRequestClass};
use {$updateRequestClass};
use {$namespaceResource};

class {$model}Controller extends Controller
{
    use ResponseJson;

    public function __construct(
        protected {$model}RepositoryInterface \${$modelSmallCase}Repo
    ) {
        //
    }

    public function index(): JsonResponse
    {
        \$data = \$this->{$modelSmallCase}Repo->all();
        return \$this->respondWithData({$model}Resource::collection(\$data), '{$model} list retrieved successfully');
    }

    public function show(int|string \$id): JsonResponse
    {
        \$data = \$this->{$modelSmallCase}Repo->find(\$id);
        if (!\$data) {
            return \$this->respondError('{$model} not found', 404);
        }
        return \$this->respondWithData({$model}Resource::make(\$data), '{$model} retrieved successfully');
    }

    public function store(Store{$model}Request \$request): JsonResponse
    {
        \$data = \$this->{$modelSmallCase}Repo->create(\$request->validated());
        return \$this->respondWithData({$model}Resource::make(\$data), '{$model} created successfully', 201);
    }

    public function update(Update{$model}Request \$request, int|string \$id): JsonResponse
    {
        \$data = \$this->{$modelSmallCase}Repo->update(\$id, \$request->validated());
        return \$this->respondWithData({$model}Resource::make(\$data), '{$model} updated successfully');
    }

    public function destroy(int|string \$id): JsonResponse
    {
        \$this->{$modelSmallCase}Repo->delete(\$id);
        return \$this->respondSuccess('{$model} deleted successfully');
    }

{$softDeleteMethods}
}
";

        // Write the generated controller file to disk
        File::put($controllerPath, $controllerStub);

        return [
            'status' => 'success',
            'message' => "✅ {$model}Controller created successfully in {$controllerDir}.",
        ];
    }
}
