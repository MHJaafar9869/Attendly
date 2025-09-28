<?php

declare(strict_types=1);

namespace App\Services\GenerateSubModule;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RequestGeneratorService
{
    /**
     * Generate Store and Update FormRequest classes for a given module and model.
     *
     * @return array
     */
    public static function generate(string $module, string $model)
    {
        // Convert model name to table name (plural snake_case)
        $table = Str::snake(Str::pluralStudly($model));

        // Base folder for Requests
        $baseFolder = module_path($module, 'app/Http/Requests');
        $modelFolder = $baseFolder."/{$model}";

        // Ensure Requests directory exists
        if (! File::isDirectory($baseFolder)) {
            File::makeDirectory($baseFolder, 0755, true);
        }
        if (! File::isDirectory($modelFolder)) {
            File::makeDirectory($modelFolder, 0755, true);
        }

        // Target paths for generated requests
        $storeRequestPath = "{$modelFolder}/Store{$model}Request.php";
        $updateRequestPath = "{$modelFolder}/Update{$model}Request.php";

        // Check that the DB table actually exists
        if (! Schema::hasTable($table)) {
            return [
                'status' => 'failed',
                'message' => "⚠️ Table '{$table}' does not exist in database. Cannot generate requests.",
            ];
        }

        // Fetch all table columns
        $columns = Schema::getColumnListing($table);

        /**
         * Rule generator closure
         */
        $generateRules = function ($isUpdate = false) use ($columns, $table, $model) {
            $rules = [];
            $routeParam = Str::camel($model); // For unique checks in Update requests

            // Define type mapping
            $typeMapping = [
                'varchar' => ['string', 'max:255'],
                'char' => ['string', 'max:255'],
                'text' => ['string'],
                'int' => ['integer'],
                'bigint' => ['integer'],
                'decimal' => ['numeric'],
                'float' => ['numeric'],
                'double' => ['numeric'],
                'boolean' => ['boolean'],
                'tinyint' => ['boolean'],
                'date' => ['date'],
                'datetime' => ['date'],
                'timestamp' => ['date'],
                'json' => ['array'],
            ];

            // Columns considered file/image uploads (plural allowed)
            $fileColumns = ['img', 'image', 'images', 'file', 'files', 'pdf', 'avatar', 'document'];

            foreach ($columns as $column) {
                // Skip system columns
                if (in_array($column, ['id', 'created_at', 'updated_at', 'deleted_at'])) {
                    continue;
                }

                // Column type and nullability
                $type = DB::selectOne(
                    'SELECT DATA_TYPE, IS_NULLABLE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = ? AND COLUMN_NAME = ?',
                    [$table, $column]
                );

                $sqlType = $type->DATA_TYPE ?? 'string';
                $isNullable = ($type->IS_NULLABLE ?? 'YES') === 'YES';

                // Start with type mapping
                $ruleSet = $typeMapping[$sqlType] ?? ['string'];

                // File/image-specific rules
                if (Str::contains($column, $fileColumns)) {
                    $ruleSet = array_merge($ruleSet, ['file', 'max:10240']); // max 10MB default
                }

                // Foreign key check for *_id columns
                if (Str::endsWith($column, '_id')) {
                    $relatedTable = Str::snake(Str::plural(Str::replaceLast('_id', '', $column)));
                    if (Schema::hasTable($relatedTable)) {
                        $ruleSet[] = "exists:{$relatedTable},id";
                    }
                }

                // Unique constraint detection (skip foreign keys)
                $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Column_name='{$column}' AND Non_unique=0");
                if (! empty($indexes) && ! Str::endsWith($column, '_id')) {
                    if ($isUpdate) {
                        $ruleSet[] = "unique:{$table},{$column},'.\$this->route('{$routeParam}').',id";
                    } else {
                        $ruleSet[] = "unique:{$table},{$column}";
                    }
                }

                // Add required/nullable logic
                if ($isUpdate) {
                    if ($isNullable) {
                        array_unshift($ruleSet, 'nullable', 'sometimes');
                    } else {
                        array_unshift($ruleSet, 'sometimes', 'required');
                    }
                } else {
                    if ($isNullable) {
                        array_unshift($ruleSet, 'nullable');
                    } else {
                        array_unshift($ruleSet, 'required');
                    }
                }

                $rules[$column] = $ruleSet;
            }

            return $rules;
        };

        // Generate and write Store/Update request stubs
        File::put($storeRequestPath, self::generateStub($module, "Store{$model}Request", $generateRules(false), $model));
        File::put($updateRequestPath, self::generateStub($module, "Update{$model}Request", $generateRules(true), $model));

        return [
            'status' => 'success',
            'message' => "✅ Store{$model}Request and Update{$model}Request created successfully inside module {$module}.",
        ];
    }

    /**
     * Build the PHP class string for a given request class.
     */
    private static function generateStub($module, $className, $rules, $modelName)
    {
        $rulesString = '';
        foreach ($rules as $col => $r) {
            $formattedRules = implode("', '", $r); // Join rule array into string
            $rulesString .= "            '{$col}' => ['{$formattedRules}'],\n";
        }

        return "<?php

namespace Modules\\{$module}\\Http\\Requests\\{$modelName};

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class {$className} extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Define validation rules for this request.
     */
    public function rules(): array
    {
        return [
{$rulesString}        ];
    }

    /**
     * Customize validation failure response.
     */
    public function failedValidation(Validator \$validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation errors',
            'errors' => \$validator->errors()
        ], 422));
    }
}
";
    }
}
