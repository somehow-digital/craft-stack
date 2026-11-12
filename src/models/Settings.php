<?php

declare(strict_types=1);

namespace SomehowDigital\Craft\Stack\models;

use craft\base\Model;
use craft\validators\ArrayValidator;

final class Settings extends Model
{
    public string $prefix = '@';

    /** @var list<array{handle?: string, path?: string, context?: array<string, mixed>}> */
    public array $namespaces = [];

    /**
     * @return list<mixed>
     */
    protected function defineRules(): array
    {
        return [
            ['prefix', 'string'],
            ['namespaces', ArrayValidator::class],
        ];
    }
}
