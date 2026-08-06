<?php

declare (strict_types = 1);

use Rector\Config\RectorConfig;
use Rector\DeadCode\Rector\If_\UnwrapFutureCompatibleIfPhpVersionRector;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use Rector\ValueObject\PhpVersion;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withPhpVersion(PhpVersion::PHP_72)
    ->withSkip([
        UnwrapFutureCompatibleIfPhpVersionRector::class,
        TypedPropertyFromStrictConstructorRector::class,
        TypedPropertyFromAssignsRector::class,

    ])
    ->withPreparedSets(
        deadCode: true,
        codeQuality: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
        // strictBooleans: true,
        // naming: true,
    )
    ->withSets([SetList::PHP_72]);
