<?php

use BitApps\WPValidator\InputDataContainer;
use BitApps\WPValidator\Rules\PresentRule;
use BitApps\WPValidator\Validator;

function collectDeprecations(callable $callback): array
{
    $deprecations = [];

    set_error_handler(function ($level, $message) use (&$deprecations) {
        $deprecations[] = $message;

        return true;
    }, E_DEPRECATED);

    try {
        $callback();
    } finally {
        restore_error_handler();
    }

    return $deprecations;
}

test('getAttributeValue without a key does not use null as an array offset', function () {
    $container = new InputDataContainer(['name' => 'John']);
    $container->setAttributeKey('name');

    $deprecations = collectDeprecations(function () use ($container) {
        $container->getAttributeValue();
    });

    expect($deprecations)->toBe([]);
    expect($container->getAttributeValue())->toBe('John');
});

test('getAttributeValue before an attribute key is set does not use null as an array offset', function () {
    $container = new InputDataContainer(['name' => 'John']);

    $deprecations = collectDeprecations(function () use ($container) {
        $container->getAttributeValue();
    });

    expect($deprecations)->toBe([]);
    expect($container->getAttributeValue())->toBeNull();
});

test('setAttributeValue before an attribute key is set does not use null as an array offset', function () {
    $container = new InputDataContainer(['name' => 'John']);

    $deprecations = collectDeprecations(function () use ($container) {
        $container->setAttributeValue('Doe');
    });

    expect($deprecations)->toBe([]);
    expect($container->getData())->toBe(['name' => 'John', '' => 'Doe']);
    expect($container->getAttributeValue())->toBe('Doe');
});

test('a null attribute key does not use null as an array offset', function () {
    $container = new InputDataContainer(['name' => 'John']);
    $container->setAttributeKey(null);

    $deprecations = collectDeprecations(function () use ($container) {
        $container->getAttributeValue();
        $container->setAttributeValue('Doe');
    });

    expect($deprecations)->toBe([]);
});

test('present rule before an attribute key is set does not use null as an array offset', function () {
    $rule = new PresentRule();
    $rule->setInputDataContainer(new InputDataContainer(['name' => 'John']));

    $deprecations = collectDeprecations(function () use ($rule) {
        $rule->validate('John');
    });

    expect($deprecations)->toBe([]);
});

test('getAttributeValue does not read an empty string key when called without a key', function () {
    $container = new InputDataContainer(['' => 'wrong', 'name' => 'John']);
    $container->setAttributeKey('name');

    expect($container->getAttributeValue())->toBe('John');
});

test('getAttributeValue reads falsy explicit keys', function () {
    $container = new InputDataContainer(['first', 'second']);
    $container->setAttributeKey(1);

    expect($container->getAttributeValue(0))->toBe('first');
    expect($container->getAttributeValue('0'))->toBe('first');
});

test('same rule compares against a field named 0', function () {
    $validator = new Validator();

    expect($validator->make(['0' => 'secret', 'confirm' => 'wrong'], ['confirm' => ['same:0']])->fails())->toBeTrue();
    expect($validator->make(['0' => 'secret', 'confirm' => 'secret'], ['confirm' => ['same:0']])->fails())->toBeFalse();
});
