<?php

declare(strict_types=1);

namespace App\Services\GenerateSubModule;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ModelSeederService
{
    /**
     * Generate a Seeder class for the given model inside the module.
     */
    public static function generate(string $module, string $model): array
    {
        $table = Str::snake(Str::pluralStudly($model));

        // Check if table exists
        if (! Schema::hasTable($table)) {
            return [
                'status' => 'failed',
                'message' => "⚠️ Table '{$table}' does not exist in database!",
            ];
        }

        // Ensure seeder folder exists
        $modelSeederFolder = module_path($module, "database/seeders/{$model}");
        if (! File::exists($modelSeederFolder)) {
            File::makeDirectory($modelSeederFolder, 0755, true);
        }

        $seederPath = "{$modelSeederFolder}/{$model}Seeder.php";

        // Skip if seeder already exists
        if (File::exists($seederPath)) {
            return [
                'status' => 'exists',
                'message' => "ℹ️  {$model}Seeder already exists!",
            ];
        }

        // Generate Seeder class with fake/sample data
        $columns = Schema::getColumnListing($table);
        $seederStub = self::generateStub($module, $model, $columns, $table);
        File::put($seederPath, $seederStub);

        // Ensure ModuleDatabaseSeeder references this seeder
        self::updateModuleSeeder($module, $model);

        // Run the generated seeder immediately
        $seederClass = "Modules\\{$module}\\database\\seeders\\{$model}\\{$model}Seeder";
        Artisan::call('db:seed', [
            '--class' => $seederClass,
            '--force' => true,
        ]);

        // Run the module's main seeder
        $moduleSeederClass = "Modules\\{$module}\\database\\seeders\\{$module}DatabaseSeeder";
        Artisan::call('db:seed', [
            '--class' => $moduleSeederClass,
            '--force' => true,
        ]);

        return [
            'status' => 'success',
            'message' => "✅ {$model}Seeder created and seeded successfully inside Module {$module}.",
        ];
    }

    /**
     * Generate Seeder stub with sample/fake data.
     */
    private static function generateStub(string $module, string $model, array $columns, string $table): string
    {
        $namespace = "Modules\\{$module}\\database\\seeders\\{$model}";

        // Detect primary key or unique indexes
        $primaryKeys = self::getPrimaryKeys($table);
        $upsertTarget = $primaryKeys === [] ? ['id'] : $primaryKeys;

        // Updatable columns: all except PKs, created_at
        $updatableColumns = array_values(array_diff($columns, array_merge($upsertTarget, ['created_at'])));

        $rows = [];

        // Generate 5 fake records
        for ($i = 1; $i <= 5; $i++) {
            $row = [];
            foreach ($columns as $col) {
                if (in_array($col, ['id', 'created_at', 'updated_at'])) {
                    continue;
                }

                $columnType = self::getColumnType($table, $col);

                // Generate data based on type and column name
                if ($columnType === 'json') {
                    $row[$col] = "json_encode(['sample' => 'Sample {$col} {$i}'])";
                } elseif (str_ends_with($col, '_id')) {
                    $related = Str::snake(Str::plural(Str::replaceLast('_id', '', $col)));
                    $row[$col] = Schema::hasTable($related)
                        ? (DB::table($related)->inRandomOrder()->value('id') ?? $i)
                        : $i;
                } elseif (str_contains($col, 'email')) {
                    $row[$col] = "user{$i}@example.com";
                } elseif (str_contains($col, 'password')) {
                    $row[$col] = "bcrypt('password123')";
                } elseif (str_contains($col, 'mobile')) {
                    $row[$col] = "01010000{$i}";
                } elseif (in_array($col, ['name', 'title'])) {
                    $row[$col] = "Sample {$col} {$i}";
                } elseif ($columnType === 'boolean') {
                    $row[$col] = $i % 2 === 0 ? 'true' : 'false';
                } else {
                    $row[$col] = "Sample {$col} {$i}";
                }
            }

            $rows[] = $row;
        }

        // Convert rows to printable PHP array
        $arrayCode = self::exportArray($rows);

        // Format the PHP seeder file
        return "<?php

namespace {$namespace};

use Illuminate\\Database\\Seeder;
use Illuminate\\Support\\Facades\\DB;

class {$model}Seeder extends Seeder
{
    public function run(): void
    {
        \$now = now();

        \$records = {$arrayCode};

        data_set(\$records, '*.created_at', \$now);
        data_set(\$records, '*.updated_at', \$now);

        DB::table('{$table}')->upsert(
            \$records,
            " . self::exportArray($upsertTarget) . ',
            ' . self::exportArray($updatableColumns) . '
        );
    }
}
';
    }

    /**
     * Detect likely upsert keys.
     *
     * - Prefer real primary keys from the schema.
     * - If no primary keys, look for unique indexes (like `name`, `slug`, `email`, etc.).
     * - Fallback to ['id'].
     */
    private static function getPrimaryKeys(string $table): array
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();

        if ($driver === 'mysql') {
            // Try to get primary keys first
            $primary = $connection->select("SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'");
            if (! empty($primary)) {
                return array_column($primary, 'Column_name');
            }

            // Try unique indexes (fallback)
            $unique = $connection->select("SHOW KEYS FROM {$table} WHERE Non_unique = 0");
            if (! empty($unique)) {
                // Return the first unique index’s columns
                $firstIndexName = $unique[0]->Key_name;

                return array_column(array_filter($unique, fn ($u) => $u->Key_name === $firstIndexName), 'Column_name');
            }
        }

        // Default fallback
        return ['id'];
    }

    /**
     * Export PHP arrays with clean formatting (no numeric keys).
     */
    private static function exportArray(array $array, int $indentLevel = 2, bool $isRoot = true): string
    {
        $indent = str_repeat('    ', $indentLevel);
        $innerIndent = str_repeat('    ', $indentLevel + 1);

        // List array (numeric keys)
        if (array_is_list($array)) {
            $lines = array_map(
                fn ($item) => is_array($item)
                    ? self::exportArray($item, $indentLevel + 1, false)
                    : str_repeat('    ', $indentLevel + 1) . var_export($item, true) . ',',
                $array
            );

            $body = implode("\n", $lines);

            return "[\n{$body}\n{$indent}]" . ($isRoot ? '' : ',');
        }

        // Associative array
        $lines = [];
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $lines[] = "{$innerIndent}'{$key}' => " . self::exportArray($value, $indentLevel + 1, false);
            } else {
                $lines[] = "{$innerIndent}'{$key}' => " . var_export($value, true) . ',';
            }
        }

        $body = implode("\n", $lines);

        return "[\n{$body}\n{$indent}]" . ($isRoot ? '' : ',');
    }

    /**
     * Ensure ModuleDatabaseSeeder calls this model seeder.
     */
    private static function updateModuleSeeder(string $module, string $model): void
    {
        $moduleSeederPath = module_path($module, "database/seeders/{$module}DatabaseSeeder.php");

        // Create if missing
        if (! File::exists($moduleSeederPath)) {
            $stub = "<?php

namespace Modules\\{$module}\\database\\seeders;

use Illuminate\\Database\\Seeder;

class {$module}DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Seeders will be added here
    }
}
";
            File::put($moduleSeederPath, $stub);
        }

        // Inject seeder call if not already present
        $content = File::get($moduleSeederPath);
        $seederClass = "{$model}\\{$model}Seeder::class";

        if (! str_contains($content, $seederClass) && preg_match('/public function run\(\): void\s*\{/', $content, $matches, PREG_OFFSET_CAPTURE)) {
            $pos = $matches[0][1] + strlen($matches[0][0]);
            $content = substr_replace($content, "\n        \$this->call({$seederClass});", $pos, 0);
            File::put($moduleSeederPath, $content);
        }
    }

    /**
     * Get database column type.
     */
    private static function getColumnType(string $table, string $column): string
    {
        return DB::getSchemaBuilder()->getColumnType($table, $column);
    }
}
