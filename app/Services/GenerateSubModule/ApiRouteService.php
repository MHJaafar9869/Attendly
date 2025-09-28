<?php

declare(strict_types=1);

namespace App\Services\GenerateSubModule;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ApiRouteService
{
    /**
     * Generate and append API resource route to a module's routes/api.php file.
     *
     * @param  string  $module  The module name (e.g. "Blog").
     * @param  string  $model  The model name (e.g. "Post").
     * @return string[]
     *
     * @throws \Exception If the api.php file already exists on first creation attempt.
     */
    public static function generate(string $module, string $model)
    {
        // Build path to the module's api.php file
        $apiFile = module_path($module, 'routes/api.php');

        // If file doesn't exist, create a fresh scaffold
        if (! File::exists($apiFile)) {
            $content = "<?php\n\nuse Illuminate\Support\Facades\Route;\n\n";
            $content .= "Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {\n";
            $content .= "    // API Resources\n";
            $content .= "});\n";
            File::put($apiFile, $content);
        } else {
            return [
                'status' => 'exists',
                'message' => "ℹ️  The api.php file already exists in module '{$module}'",
            ];
        }

        // Get the current contents of api.php
        $content = File::get($apiFile);

        // Create plural and singular snake_case names for route and alias
        $plural = Str::plural(Str::snake($model));   // e.g. Language -> languages
        $name = Str::snake($model);                         // e.g. Language -> language

        // Build full controller namespace
        $controllerNamespace = "Modules\\{$module}\\Http\\Controllers\\Api\\{$model}\\{$model}Controller";
        $controllerUse = "use {$controllerNamespace};";

        // Insert controller `use` if not already present
        if (! str_contains($content, $controllerUse)) {
            $content = preg_replace(
                '/(use Illuminate\\\\Support\\\\Facades\\\\Route;)/',
                "$1\n{$controllerUse}",
                $content,
                1
            );
        }

        // Build the route line
        $routeLine = "    Route::apiResource('{$plural}', {$model}Controller::class)->names('{$name}');";

        // Add route line if not present
        if (! str_contains($content, $routeLine)) {
            $pattern = "/(Route::(?:middleware\(\['auth:sanctum'\]\)->)?prefix\('v1'\)->group\(function \(\) \{)/";

            // If group already exists, inject route inside it
            if (preg_match($pattern, $content)) {
                $content = preg_replace(
                    $pattern,
                    "$1\n{$routeLine}",
                    $content
                );
            } else {
                // Otherwise, create the group and add the route
                $content .= "\nRoute::middleware(['auth:sanctum'])->prefix('v1')->group(function () {\n";
                $content .= "{$routeLine}\n";
                $content .= "});\n";
            }
        }

        // Save updated content back into the file
        File::put($apiFile, $content);

        return [
            'status' => 'success',
            'message' => "✅ Route for {$model} added successfully in {$module}/Routes/api.php",
        ];
    }
}
