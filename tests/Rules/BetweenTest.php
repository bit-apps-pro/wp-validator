<?php

use BitApps\WPValidator\Rules\BetweenRule;

test('between', function () {

    $rule = new BetweenRule();
    $paramKeys = ['min', 'max'];
    $paramValues = [1, 5];
    $rule->setParameterValues($paramKeys, $paramValues);
    expect(true)->toBe($rule->validate('abcde'));
    expect(true)->toBe($rule->validate(3));
    expect(true)->toBe($rule->validate(5));
    expect(true)->toBe($rule->validate([1, 2, 3, 4, 5])); // arrays are measured by count, not values

    expect(false)->toBe($rule->validate('abcdef'));
    expect(false)->toBe($rule->validate(6));
    expect(false)->toBe($rule->validate([1, 2, 3, 4, 5, 6]));
});

test('between accepts a length of zero', function () {

    $rule = new BetweenRule();
    $rule->setParameterValues(['min', 'max'], [0, 5]);

    // A length of 0 is a valid length, not a failure.
    expect(true)->toBe($rule->validate(0));
    expect(true)->toBe($rule->validate(0.0));
    expect(true)->toBe($rule->validate(''));
    expect(true)->toBe($rule->validate([]));

});

test('between rejects values with no measurable length', function () {

    $rule = new BetweenRule();
    $rule->setParameterValues(['min', 'max'], [0, 5]);

    expect(false)->toBe($rule->validate(true));
    expect(false)->toBe($rule->validate(null));
    expect(false)->toBe($rule->validate(new stdClass()));

});
