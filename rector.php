<?php

declare(strict_types=1);

use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector;
use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;
use Rector\ValueObject\PhpVersion;
use RectorLaravel\Set\LaravelLevelSetList;

return static function (RectorConfig $rectorConfig): void {
    // Paths to analyze
    $paths = [
        __DIR__ . '/app',
        __DIR__ . '/config',
        __DIR__ . '/database',
        __DIR__ . '/resources',
        __DIR__ . '/routes',
        __DIR__ . '/tests',
    ];

    foreach (['Core', 'Domain', 'Feature', 'UI'] as $module) {
        foreach (['app', 'database', 'resources', 'routes'] as $dir) {
            $path = __DIR__ . "/Modules/{$module}/{$dir}";
            if (is_dir($path)) {
                $paths[] = $path;
            }
        }
    }

    $rectorConfig->paths($paths);

    // Skip specific rules
    $rectorConfig->skip([
        CompactToVariablesRector::class,
    ]);

    // Enable caching for Rector
    $rectorConfig->cacheDirectory(__DIR__ . '/storage/rector');
    $rectorConfig->cacheClass(FileCacheStorage::class);

    // Apply sets for Laravel and general code quality
    $rectorConfig->sets([
        LaravelLevelSetList::UP_TO_LARAVEL_120,
        SetList::CODE_QUALITY,
    ]);

    // Define PHP version for Rector
    $rectorConfig->phpVersion(PhpVersion::PHP_84);
};
