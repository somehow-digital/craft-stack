<?php

use SomehowDigital\Craft\Stack\models\Settings;

it('has no namespaces by default', function () {
	$settings = new Settings();

	expect($settings->prefix)->toBe('@');
	expect($settings->namespaces)->toBe([]);
});

it('accepts a prefix and namespaces', function () {
	$settings = new Settings([
		'prefix' => '#',
		'namespaces' => [['handle' => 'theme', 'path' => '_theme']],
	]);

	expect($settings->validate())->toBeTrue();
});
