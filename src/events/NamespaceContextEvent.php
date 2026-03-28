<?php

namespace SomehowDigital\Craft\Stack\events;

use craft\base\Event;

class NamespaceContextEvent extends Event
{
	public array $namespace;
    public array $context = [];
}
