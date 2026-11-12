---
title: Dynamic resolution
description: Include templates by their plain path and let the namespaces decide.
---

Templates are looked up in the order of the configured [namespaces](/guide/settings/), and the first existing file wins.

With the example configuration, the current site `mysite` and the site group `mygroup`, this resolves to the first existing file of:

1. `templates/sites/mysite/header.twig`
2. `templates/groups/mygroup/header.twig`
3. `templates/_global/header.twig`

```twig
{% include 'header.twig' %}
```

If none exists, Craft throws its usual `TemplateNotFound` exception.

:::caution
Craft resolves existing template files first and consults the namespaces only afterwards. A namespace therefore never overrides a template that exists at the plain path.
:::
