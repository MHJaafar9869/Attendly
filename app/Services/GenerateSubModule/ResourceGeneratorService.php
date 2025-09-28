<?php

declare(strict_types=1);

namespace App\Services\GenerateSubModule;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ResourceGeneratorService
{
    /**
     * Generate a JSON Resource class for a given module and model.
     *
     * @param  string  $module  The module name (e.g. "Blog").
     * @param  string  $model  The model name (e.g. "Post").
     * @return string[]
     */
    public static function generate(string $module, string $model)
    {
        // Convert model name to table name (plural snake_case)
        $table = Str::snake(Str::pluralStudly($model)); // Example: Profile -> profiles

        // Ensure table exists before generating resource
        if (! Schema::hasTable($table)) {
            return [
                'status' => 'failed',
                'message' => "⚠️ Table '{$table}' does not exist in database. Cannot generate resource.",
            ];
        }

        // Define base and model folders for transformers
        $baseFolder = module_path($module, 'app/Transformers');
        $modelFolder = $baseFolder."/{$model}";

        // Create directories if missing
        if (! File::exists($modelFolder)) {
            File::makeDirectory($modelFolder, 0755, true);
        }

        // Define target resource path
        $resourcePath = "{$modelFolder}/{$model}Resource.php";
        $minResourcePath = "{$modelFolder}/{$model}MinResource.php";

        // Prevent overwriting existing resource
        if (File::exists($resourcePath) && File::exists($minResourcePath)) {
            return [
                'status' => 'exists',
                'message' => "ℹ️  {$model}(w. min)Resource already exists in {$modelFolder}.",
            ];
        }

        // Get list of DB columns
        $columns = Schema::getColumnListing($table);

        // Build the resource class stub
        $resourceStub = self::generateStub($module, $model, $columns);
        $resourceMinStub = self::generateStub($module, $model, $columns, 'Min');

        // Write file to disk
        File::put($resourcePath, $resourceStub);
        File::put($minResourcePath, $resourceMinStub);

        return [
            'status' => 'success',
            'message' => "✅ {$model}Resource created successfully inside Module {$module}.",
        ];
    }

    /**
     * Build the PHP class string for the resource.
     *
     * @param  string  $module
     * @param  array  $columns
     * @return string
     */
    private static function generateStub($module, $model, $columns, string $classPrefix = '')
    {
        $fieldsString = '';
        $table = Str::snake(Str::pluralStudly($model));

        // Loop through each column and map how it should appear in the resource
        foreach ($columns as $col) {
            $type = Schema::getColumnType($table, $col);

            if (Str::endsWith($col, '_id')) {
                // Foreign key -> expose relation name instead of raw id
                $relation = Str::camel(Str::replaceLast('_id', '', $col));
                $fieldsString .= "            '{$relation}' => \$resource->{$relation}?->name,\n";
            } elseif ($type === 'json') {
                // JSON column -> treat as translatable field
                $fieldsString .= "            '{$col}' => \$this->getTranslations('{$col}'),\n";
            } else {
                // Default -> expose column directly
                $fieldsString .= "            '{$col}' => \$resource->{$col},\n";
            }
        }

        // Return full PHP class stub
        return "<?php

namespace Modules\\{$module}\\Transformers\\{$model};

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class {$model}{$classPrefix}Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request \$request)
    {
        \$resource = \$this->resource;

        return [
{$fieldsString}        ];
    }
}
";
    }
}
