<?php

use BitApps\WPValidator\Helpers;

class HelpersTestHarness
{
    use Helpers;
}

test('sets a nested array element by reference', function () {
    $helpers = new HelpersTestHarness();
    $data = [];

    $result = $helpers->setNestedElement($data, ['profile', 'name'], 'Ada');

    expect($result)->toBe('Ada')
        ->and($data)->toBe([
            'profile' => [
                'name' => 'Ada',
            ],
        ]);
});

test('sets a nested object property by reference', function () {
    $helpers = new HelpersTestHarness();
    $data = new stdClass();

    $result = $helpers->setNestedElement($data, ['profile', 'name'], 'Ada');

    expect($result)->toBe('Ada')
        ->and($data)->toEqual((object) [
            'profile' => (object) [
                'name' => 'Ada',
            ],
        ]);
});
