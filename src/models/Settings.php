<?php

namespace SomehowDigital\Craft\Stack\models;

use craft\base\Model;
use craft\validators\ArrayValidator;

class Settings extends Model
{
	public string $prefix = '@';
	public array $namespaces = [];

	public function defineRules(): array
	{
		return [
			['prefix', 'string'],
			['namespaces', ArrayValidator::class],
		];
	}
}
