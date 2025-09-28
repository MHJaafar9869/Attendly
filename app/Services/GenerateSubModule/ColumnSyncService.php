<?php

declare(strict_types=1);

namespace App\Services\GenerateSubModule;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ColumnSyncService
{
    /**
     * Sync database table columns into the `columns` table with multilingual labels.
     *
     * @param  string  $module  The parent module name (currently unused, but kept for context).
     * @param  string  $submodule  The submodule/model name (used to derive the table).
     * @return string[]
     */
    public static function generate(string $module, string $model)
    {
        // Derive table name from model: plural + snake_case
        // e.g. "Category" -> "categories"
        $table = Str::snake(Str::pluralStudly($model));

        // Ensure the table exists before proceeding
        if (! Schema::hasTable($table)) {
            return [
                'status' => 'failed',
                'message' => "⚠️ Table '{$table}' does not exist, skipping service",
            ];
        }

        // Ensure the `columns` table exists, otherwise throw an exception
        if (! Schema::hasTable('columns')) {
            return [
                'status' => 'failed',
                'message' => "⚠️ The 'columns' table does not exist. Cannot sync columns for '{$model}'.",
            ];
        }

        // Get list of columns in the table
        $columns = Schema::getColumnListing($table);

        // Columns to ignore (system fields)
        $ignore = ['id', 'created_at', 'updated_at', 'deleted_at'];

        // Translator instance (English -> Arabic)
        $tr = new GoogleTranslate('ar');

        foreach ($columns as $col) {
            // Skip system columns
            if (in_array($col, $ignore)) {
                continue;
            }

            // Build multilingual keys and labels
            $arabicKey = $tr->translate($col);
            $arabicLabel = $tr->translate(Str::title(str_replace('_', ' ', $col)));

            $key = [
                'en' => $col,
                'ar' => $arabicKey,
            ];

            $label = [
                'en' => Str::title(str_replace('_', ' ', $col)),
                'ar' => $arabicLabel,
            ];

            // Check if this column already exists in the `columns` table
            $exists = DB::table('columns')
                ->where('model', $model)
                ->whereJsonContains('key->en', $col)
                ->exists();

            // If it doesn't exist, insert a new record
            if (! $exists) {
                DB::table('columns')->insert([
                    'model' => $model,
                    'key' => json_encode($key, JSON_UNESCAPED_UNICODE),
                    'label' => json_encode($label, JSON_UNESCAPED_UNICODE),
                    'sortable' => true,
                    'filterable' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return [
            'status' => 'success',
            'message' => "✅ Columns synced for {$model}",
        ];
    }
}
