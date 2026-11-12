---
title: Settings
description: Define the namespaces that Stack resolves templates from.
---

Create a `config/stack.php` file:

```php
<?php

use craft\helpers\App;

return [
	// 'prefix' => '@',
	'namespaces' => [
		[
			'handle' => '{site.handle|lower}',
			'path' => 'sites/{site.handle|lower}',
		],
		[
			'handle' => '{group.name|lower}',
			'path' => 'groups/{group.name|lower}',
		],
		[
			'handle' => 'global',
			'path' => '_global/{env}',
			'context' => [
				'env' => App::devMode() ? 'dev' : '',
			],
		],
	],
];
```

| Setting      | Default | Description                                                                           |
| ------------ | ------- | ------------------------------------------------------------------------------------- |
| `prefix`     | `@`     | The prefix of namespace handles in [static resolution](/features/static-resolution/). |
| `namespaces` | `[]`    | The list of namespaces. Without any, the plugin doesn't resolve templates.            |

Each namespace supports these keys:

| Key       | Description                                                                                |
| --------- | ------------------------------------------------------------------------------------------ |
| `path`    | The template folder, relative to your `templates` folder.                                  |
| `handle`  | Optional. Needed for [static resolution](/features/static-resolution/).                    |
| `context` | Optional. Variables for the [object templates](/features/context/) of `handle` and `path`. |

The order of the namespaces matters for [dynamic resolution](/features/dynamic-resolution/).
