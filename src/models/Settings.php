<?php

namespace SomehowDigital\Craft\Stack\models;

use craft\base\Model;

class Settings extends Model
{
	public array $namespaces = [];

	public function defineRules(): array
	{
		return [
			['namespaces', 'array'],
		];
	}
}
