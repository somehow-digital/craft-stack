<?php

declare(strict_types=1);

namespace SomehowDigital\Craft\Stack\events;

use craft\base\Event;

final class NamespaceContextEvent extends Event
{
    /** @var array{handle?: string, path?: string, context?: array<string, mixed>} */
    public array $namespace = [];

    /** @var array<string, mixed> */
    public array $context = [];
}
