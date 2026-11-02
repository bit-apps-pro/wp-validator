<?php

use BitApps\WPValidator\Rules\SortDirectionRule;

dataset('valid sort directions', [
    'asc',
    'ASC',
    'desc',
    'DESC',
]);

dataset('invalid sort directions', [
    ' asc',
    'desc ',
    [['asc']],
    null,
    'ascending',
    'desc; DROP',
    'desc nulls last',
]);

test('accepts asc and desc sort directions case-insensitively', function ($value) {
    $rule = new SortDirectionRule();

    expect($rule->validate($value))->toBeTrue();
})->with('valid sort directions');

test('rejects values that are not exact sort directions', function ($value) {
    $rule = new SortDirectionRule();

    expect($rule->validate($value))->toBeFalse();
})->with('invalid sort directions');
