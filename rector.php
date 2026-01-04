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
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\AddReturnDocblockForArrayDimAssignedObjectRector;
use Rector\TypeDeclarationDocblocks\Rector\ClassMethod\DocblockReturnArrayFromDirectArrayInstanceRector;
use RectorLaravel\Set\LaravelSetProvider;

return RectorConfig::configure()
    ->withImportNames()
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
    ])

    ->withSetProviders(LaravelSetProvider::class)
    ->withComposerBased(laravel: true)
    ->withPaths([
        __DIR__ . '/app',
        __DIR__ . '/routes',
    ])
    ->withPHPStanConfigs([
        __DIR__ . '/phpstan.neon',
    ])
    ->withSkip([
        __DIR__ . '/storage',
        __DIR__ . '/bootstrap/cache',
        __DIR__ . '/vendor',
    ]);