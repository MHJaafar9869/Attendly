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
        $rows = '';

        // Generate 5 rows of sample data
        for ($i = 1; $i <= 5; $i++) {
            $dataString = '';

            foreach ($columns as $col) {
                if (in_array($col, ['id', 'created_at', 'updated_at'])) {
                    continue;
                }

                $columnType = self::getColumnType($table, $col);

                // Generate values based on column type and name
                if ($columnType === 'json') {
                    $value = "json_encode(['sample' => 'Sample {$col} {$i}'])";
                } elseif (str_ends_with($col, '_id')) {
                    $relatedTable = Str::snake(Str::plural(Str::replaceLast('_id', '', $col)));
                    if (Schema::hasTable($relatedTable)) {
                        $ids = DB::table($relatedTable)->pluck('id')->toArray();
                        $value = count($ids) ? $ids[array_rand($ids)] : $i;
                    } else {
                        $value = $i;
                    }
                } elseif ($col === 'name') {
                    $value = "Sample {$col} {$i}";
                } elseif (str_contains($col, 'email')) {
                    $value = "user{$i}@example.com";
                } elseif (str_contains($col, 'mobile')) {
                    $value = "01010000{$i}";
                } elseif ($col === 'username') {
                    $value = "user{$i}";
                } elseif ($col === 'full_name') {
                    $value = "Sample Name {$i}";
                } elseif ($col === 'password') {
                    $value = "bcrypt('password123')";
                } elseif ($col === 'gender') {
                    $value = $i % 2 === 0 ? 'Male' : 'Female';
                } else {
                    $value = "Sample {$col} {$i}";
                }

                // Raw PHP for JSON/password, string for others
                if ($columnType === 'json' || $col === 'password') {
                    $dataString .= "                '{$col}' => {$value},\n";
                } else {
                    $dataString .= "                '{$col}' => '{$value}',\n";
                }
            }

            $rows .= "        {$model}::firstOrCreate([\n{$dataString}        ]);\n\n";
        }

        $namespace = "Modules\\{$module}\\database\\seeders\\{$model}";

        return "<?php

namespace {$namespace};

use Illuminate\\Database\\Seeder;
use Modules\\{$module}\\Models\\{$model};

class {$model}Seeder extends Seeder
{
    public function run(): void
    {
{$rows}    }
}
";
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
