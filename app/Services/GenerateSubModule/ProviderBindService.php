<?php

declare(strict_types=1);

namespace App\Services\GenerateSubModule;

use Illuminate\Support\Facades\File;

class ProviderBindService
{
    /**
     * Inject repository binding and `use` statements into a module's ServiceProvider.
     *
     * @param  string  $module  The module name (e.g. "Blog").
     * @param  string  $model  The model name (e.g. "Post").
     * @return string[]
     */
    public static function generate(string $module, string $model)
    {
        // Path to the ServiceProvider of the given module
        $providerPath = module_path($module, "app/Providers/{$module}ServiceProvider.php");

        // Fail explicitly if ServiceProvider is missing
        if (! File::exists($providerPath)) {
            return [
                'status' => 'failed',
                'message' => "⚠️ ServiceProvider for {$module} not found at {$providerPath}.",
            ];
        }

        // Read current content
        $content = File::get($providerPath);

        // Expected namespaces
        $interfaceNamespace = "Modules\\{$module}\\Repositories\\{$model}\\{$model}RepositoryInterface";
        $repositoryNamespace = "Modules\\{$module}\\Repositories\\{$model}\\{$model}Repository";

        // Corresponding `use` statements
        $interfaceUse = "use {$interfaceNamespace};";
        $repositoryUse = "use {$repositoryNamespace};";

        // If `use` lines not already present, insert them right after namespace declaration
        if (! str_contains($content, $interfaceUse)) {
            $content = preg_replace(
                '/(namespace\s+Modules\\\\'.$module.'\\\\Providers;)/',
                "$1\n\n{$interfaceUse}\n{$repositoryUse}",
                $content,
                1
            );
        }

        // Binding line to register repository interface to implementation
        $bindLine = "        \$this->app->bind({$model}RepositoryInterface::class, {$model}Repository::class);";

        // Add binding inside `register()` if not already there
        if (! str_contains($content, $bindLine)) {
            $content = preg_replace_callback(
                '/public function register\(\): void\s*\{(.*?)\}/s',
                function ($matches) use ($bindLine) {
                    $body = trim($matches[1]);

                    // Avoid duplicate bindings
                    if (str_contains($body, $bindLine)) {
                        return $matches[0];
                    }

                    // Append binding
                    $body .= "\n{$bindLine}";

                    return "public function register(): void {\n{$body}\n}";
                },
                $content
            );
        }

        // Save the modified ServiceProvider
        File::put($providerPath, $content);

        return [
            'status' => 'success',
            'message' => "✅ Repository binding and imports for {$model} added successfully in {$module}ServiceProvider.",
        ];
    }
}
