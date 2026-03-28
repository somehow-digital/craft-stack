# `Stack` for Craft CMS
> Brings a namespace system to better organize and load [Twig](https://twig.symfony.com/) templates in [Craft CMS](https://craftcms.com/).

`Stack` provides a namespace system to resolve template files and behaves similar to how Twig's native
[FilesystemLoader](https://twig.symfony.com/doc/3.x/api.html#twig-loader-filesystemloader) works. Under the
hood it uses Craft's [Template Roots](https://craftcms.com/docs/5.x/extend/template-roots.html) mechanism.
Currently, this plugin works in frontend mode only. (It does not work with Craft's panel templates.)

## Requirements

* Craft CMS 5.8.0 or later.
* PHP 8.2 or later.

## Installation

Install this plugin from the Plugin Store or via Composer.

#### Plugin Store

Go to the “Plugin Store” in your project’s Control Panel, search for
“stack” and click on the “Install” button in its modal window.

#### Composer

```sh
composer require somehow-digital/craft-stack
./craft plugin/install stack
```

## Configuration

Settings can be configured via a `config/stack.php` config file.

> **`prefix`** - handle prefix for namespaced template file paths.

_default_: `@`

> **`namespaces`** - list of namespaces for template file path resolution.

_default_: `[]`

* By default, no namespaces are configured, and the plugin will not resolve templates.
* Order of namespaces matters when template file paths are resolved via `Dynamic Resolution`.
* `handle` and `path` values can use object templates, where `Site` and `SiteGroup` objects are available to use.
* `handle` values are optional but are needed if `Static Resolution` is to be used.

**config/stack.php**
```php
<?php

return [
	// 'prefix' => '@',
	'namespaces' => [
		[
			'handle' => '{handle|lower}',
			'path' => 'sites/{handle|lower}',
		],
		[
			'handle' => '{group.name|lower}',
			'path' => 'groups/{group.name|lower}',
		],
		[
			'handle' => 'global',
			'path' => '_global',
		],
	],
];
```

## Usage

> Due to how Craft's template roots work, if a defined template path resolves to an existing
> template file, Stack's namespace system will not be used, because Craft will resolve
> existing template files, before configured template roots are evaluated. This also means
> that Stack will not interfere with template paths of existing template files.

### Dynamic Resolution

The template file will be resolved by the order of the configured namespaces and their paths.

1. If the current site's handle is `mysite` and the current site-group's name is `mygroup`,
   the first resolved template path is `templates/sites/mysite/header.twig`. If this
   template file exists, it will be used.
2. If it doesn't exist, the next resolved template path is `templates/groups/mygroup/header.twig`.
   If this template file exists, it will be used.
3. If it doesn't exist, the last resolved template path is `templates/_global/header.twig`.
   If this template file exists, it will be used.
4. If it doesn't exist, Craft will throw the default `TemplateNotFound` exception.

```twig
{# resolves to `templates/sites/craft/header.twig` or `templates/groups/craft/header.twig` or `templates/_global/header.twig` #}
{% include 'header.twig' %}
```

### Static Resolution

Prefixing a template file path with a namespace handle, a template file for a specific namespace can be used.
If the template does not exist, Craft will throw a `TemplateNotFound` exception.
The namespace prefix will be evaluated via the `handle` value defined in the config file.

1. If the current site's handle is `mysite`, the resolved template path is `templates/sites/mysite/header.twig`.
   If this template file exists, it will be used.
2. If it doesn't exist, Craft will throw the default `TemplateNotFound` exception.

```twig
{# resolves to `templates/groups/mygroup/header.twig` #}
{% include '@mygroup/header.twig' %}
```

```twig
{# resolves to `templates/sites/mysite/header.twig` #}
{% include '@mysite/header.twig' %}
```

```twig
{# resolves to `templates/_global/header.twig` #}
{% include '@global/header.twig' %}
```
