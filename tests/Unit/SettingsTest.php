<?php

declare(strict_types=1);

use SomehowDigital\Craft\Stack\models\Settings;

it('has no namespaces by default', function (): void {
    $settings = new Settings();

    expect($settings->prefix)->toBe('@');
    expect($settings->namespaces)->toBe([]);
});

it('accepts a prefix and namespaces', function (): void {
    $settings = new Settings([
        'prefix' => '#',
        'namespaces' => [['handle' => 'theme', 'path' => '_theme']],
    ]);

    expect($settings->validate())->toBeTrue();
});
