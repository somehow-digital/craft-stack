---
title: Object templates
description: Build handles and paths from the current site, group or your own variables.
---

The `handle` and `path` of a namespace are [object templates](https://craftcms.com/docs/5.x/system/object-templates.html). These variables are available:

| Variable | Description                                       |
| -------- | ------------------------------------------------- |
| `site`   | The current site.                                 |
| `group`  | The group of the current site, or `null`.         |
| …        | Any variable from the `context` of the namespace. |

A namespace whose `path` renders empty is skipped, and so is the handle if it renders empty.

## Event

Change the context of a namespace in code with the `EVENT_NAMESPACE_CONTEXT` event:

```php
use SomehowDigital\Craft\Stack\events\NamespaceContextEvent;
use SomehowDigital\Craft\Stack\Stack;
use yii\base\Event;

Event::on(
	Stack::class,
	Stack::EVENT_NAMESPACE_CONTEXT,
	function (NamespaceContextEvent $event): void {
		$event->context['env'] = 'staging';
	},
);
```

`$event->namespace` holds the configuration of the namespace being resolved.
