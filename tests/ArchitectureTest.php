<?php

use BitApps\WPValidator\Rule;

arch('globals')
    ->expect(['die', 'error_log', 'var_dump', 'exit', 'print_r'])
    ->not->toBeUsed();

arch('should expect extend Rule class')
    ->expect('BitApps\WPValidator\Rules')
    ->toExtend(Rule::class);

arch('should expect validate methods')
    ->expect('BitApps\WPValidator\Rules')
    ->toHaveMethod('validate');

arch('should expect message methods')
    ->expect('BitApps\WPValidator\Rules')
    ->toBeClasses()
    ->toHaveMethod('message');

arch('should expect class name to end with Rule')
    ->expect('BitApps\WPValidator\Rules')
    ->toBeClasses()
    ->toHaveSuffix('Rule');
