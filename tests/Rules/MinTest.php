<?php

use BitApps\WPValidator\Rules\MinRule;

test('min', function () {

    $rule = new MinRule();
    $paramKeys = ['min'];
    $paramValues = [5];

    $rule->setParameterValues($paramKeys, $paramValues);

    expect(true)->toBe($rule->validate('1103d'));
    expect(true)->toBe($rule->validate('passd'));
    expect(true)->toBe($rule->validate('password'));
    expect(true)->toBe($rule->validate(5));
    expect(true)->toBe($rule->validate([1, 2, 3, 4, 5]));

    expect(false)->toBe($rule->validate('pass'));
    expect(false)->toBe($rule->validate(4));
    expect(false)->toBe($rule->validate([1, 2, 3, 4]));

});

test('min accepts a length of zero', function () {

    $rule = new MinRule();
    $rule->setParameterValues(['min'], [0]);

    // A length of 0 is a valid length, not a failure.
    expect(true)->toBe($rule->validate(0));
    expect(true)->toBe($rule->validate(0.0));
    expect(true)->toBe($rule->validate(''));
    expect(true)->toBe($rule->validate([]));

});

test('min rejects values with no measurable length', function () {

    $rule = new MinRule();
    $rule->setParameterValues(['min'], [0]);

    expect(false)->toBe($rule->validate(true));
    expect(false)->toBe($rule->validate(null));
    expect(false)->toBe($rule->validate(new stdClass()));

});
