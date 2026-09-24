<?php

use BitApps\WPValidator\Rules\SqlIdentifierRule;
use BitApps\WPValidator\Validator;

dataset('valid SQL identifiers', [
    'id',
    '_private_column',
    'custom_field_12',
    'owner_ID',
]);

dataset('invalid SQL identifiers', [
    'IF(1=1,SLEEP(5),id)',
    'id`,(SELECT sleep(22) )#',
    'id; DROP TABLE users',
    'users.id',
    '`id`',
    '1column',
    'id-name',
    '',
    null,
    [['id']],
]);

test('accepts a single valid SQL identifier segment', function ($value) {
    $rule = new SqlIdentifierRule();

    expect($rule->validate($value))->toBeTrue();
})->with('valid SQL identifiers');

test('rejects values that are not a single SQL identifier segment', function ($value) {
    $rule = new SqlIdentifierRule();

    expect($rule->validate($value))->toBeFalse();
})->with('invalid SQL identifiers');

test('resolves sql_identifier and returns its validation message', function () {
    $validator = new Validator();

    $validation = $validator->make(
        ['column' => 'users.id'],
        ['column' => ['sql_identifier']]
    );

    expect($validation->errors())->toBe([
        'column' => ['The column must be a valid SQL identifier'],
    ]);
});
