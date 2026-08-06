<?php

use BitApps\WPValidator\Validator;

dataset('nullable SQL structure values', [
    'omitted field' => [[], [], false],
    'null' => [['value' => null], ['value' => null], false],
    'empty string' => [['value' => ''], ['value' => ''], false],
    'empty array' => [['value' => []], ['value' => []], false],
    'false' => [['value' => false], null, true],
]);

test('validates safe SQL structure values without changing them', function () {
    $validator = (new Validator())->make(
        ['sortBy' => 'custom_field_12', 'sortOrder' => 'DESC'],
        [
            'sortBy' => ['required', 'sql_identifier'],
            'sortOrder' => ['required', 'sort_direction'],
        ]
    );

    expect($validator->fails())->toBeFalse()
        ->and($validator->validated())->toBe([
            'sortBy' => 'custom_field_12',
            'sortOrder' => 'DESC',
        ]);
});

test('returns the first labeled error for each malicious SQL structure value', function () {
    $validator = (new Validator())->make(
        [
            'sortBy' => 'id`,(SELECT sleep(22) )#',
            'sortOrder' => 'desc; DROP',
        ],
        [
            'sortBy' => ['required', 'sql_identifier'],
            'sortOrder' => ['required', 'sort_direction'],
        ],
        [],
        [
            'sortBy' => 'sort column',
            'sortOrder' => 'sort direction',
        ]
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors())->toBe([
            'sortBy' => ['The sort column must be a valid SQL identifier'],
            'sortOrder' => ['The sort direction must be asc or desc'],
        ])
        ->and($validator->validated())->toBe([
            'sortBy' => ['The sort column must be a valid SQL identifier'],
            'sortOrder' => ['The sort direction must be asc or desc'],
        ]);
});

test('nullable skips SQL structure validation only for values considered empty', function ($rule, $data, $expectedValidated, $fails) {
    $validator = (new Validator())->make(
        $data,
        ['value' => ['nullable', $rule]]
    );

    expect($validator->fails())->toBe($fails);

    if ($fails) {
        expect($validator->errors())->toHaveKey('value');

        return;
    }

    expect($validator->validated())->toBe($expectedValidated);
})->with([
    'sql identifier' => ['sql_identifier'],
    'sort direction' => ['sort_direction'],
])->with('nullable SQL structure values');
