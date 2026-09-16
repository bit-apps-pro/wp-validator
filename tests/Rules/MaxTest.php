<?php

use BitApps\WPValidator\Rules\MaxRule;

test('max', function () {

    $rule = new MaxRule();

    $paramKeys = ['max'];
    $paramValues = [5];

    $rule->setParameterValues($paramKeys, $paramValues);
    expect(true)->toBe($rule->validate('110'));
    expect(true)->toBe($rule->validate('passw'));
    expect(true)->toBe($rule->validate(5));
    expect(true)->toBe($rule->validate([4, 5, 6]));

    expect(false)->toBe($rule->validate('password'));
    expect(false)->toBe($rule->validate(6));
    expect(false)->toBe($rule->validate([1, 2, 3, 4, 5, 6]));

});

test('max accepts a length of zero', function () {

    $rule = new MaxRule();
    $rule->setParameterValues(['max'], [5]);

    // A length of 0 is within any non-negative maximum.
    expect(true)->toBe($rule->validate(0));
    expect(true)->toBe($rule->validate(0.0));
    expect(true)->toBe($rule->validate(''));
    expect(true)->toBe($rule->validate([]));

});

test('max rejects values with no measurable length', function () {

    $rule = new MaxRule();
    $rule->setParameterValues(['max'], [5]);

    expect(false)->toBe($rule->validate(true));
    expect(false)->toBe($rule->validate(null));
    expect(false)->toBe($rule->validate(new stdClass()));

});
