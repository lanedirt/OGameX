<?php

use Rector\CodeQuality\Rector\Concat\JoinStringConcatRector;
use Rector\CodeQuality\Rector\FuncCall\CompactToVariablesRector;
use Rector\CodeQuality\Rector\Identical\StrlenZeroToIdenticalEmptyStringRector;
use Rector\CodingStyle\Rector\Assign\NestedTernaryToMatchRector;
use Rector\Config\RectorConfig;
use Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector;
use Rector\Privatization\Rector\ClassMethod\PrivatizeFinalClassMethodRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeDeclarationRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationRector;
use RectorLaravel\Rector\BooleanNot\AvoidNegatedCollectionContainsOrDoesntContainRector;
use RectorLaravel\Rector\Coalesce\ApplyDefaultInsteadOfNullCoalesceRector;
use RectorLaravel\Rector\Expr\AppEnvironmentComparisonToParameterRector;
use RectorLaravel\Rector\FuncCall\RemoveDumpDataDeadCodeRector;
use RectorLaravel\Rector\MethodCall\EloquentOrderByToLatestOrOldestRector;
use RectorLaravel\Rector\StaticCall\CarbonSetTestNowToTravelToRector;
use RectorLaravel\Rector\StaticCall\CarbonToDateFacadeRector;
use RectorLaravel\Set\LaravelSetProvider;

return RectorConfig::configure()
    ->withImportNames()
    ->withParallel()
    ->withRules([
        // Types
        AddReturnTypeDeclarationRector::class,
        AddParamTypeDeclarationRector::class,
        NestedTernaryToMatchRector::class,
        // Strictness
        PrivatizeFinalClassMethodRector::class,
        ClassPropertyAssignToConstructorPromotionRector::class,
        JoinStringConcatRector::class,
        StrlenZeroToIdenticalEmptyStringRector::class,
        CompactToVariablesRector::class,
        // Laravel specific
        AppEnvironmentComparisonToParameterRector::class,
        ApplyDefaultInsteadOfNullCoalesceRector::class,
        AvoidNegatedCollectionContainsOrDoesntContainRector::class,
        CarbonSetTestNowToTravelToRector::class,
        CarbonToDateFacadeRector::class,
        EloquentOrderByToLatestOrOldestRector::class,
        RemoveDumpDataDeadCodeRector::class,
    ])

    ->withSetProviders(LaravelSetProvider::class)
    ->withComposerBased(laravel: true)
    ->withPaths([
        __DIR__ . '/app',
    ])
    ->withSkip([
        __DIR__ . '/storage',
        __DIR__ . '/bootstrap/cache',
        __DIR__ . '/vendor',
    ]);
