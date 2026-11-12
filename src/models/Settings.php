<?php

declare(strict_types=1);

namespace SomehowDigital\Craft\Stack\models;

use craft\base\Model;

final class Settings extends Model
{
    public string $prefix = '@';

    /** @var list<array{handle?: string, path?: string, context?: array<string, mixed>}> */
    public array $namespaces = [];
}
