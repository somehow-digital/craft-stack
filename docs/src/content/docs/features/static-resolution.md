---
title: Static resolution
description: Load a template from one specific namespace.
---

Prefix a template path with a namespace `handle` to skip the lookup order and use that namespace only:

```twig
{# templates/groups/mygroup/header.twig #}
{% include '@mygroup/header.twig' %}

{# templates/sites/mysite/header.twig #}
{% include '@mysite/header.twig' %}

{# templates/_global/header.twig #}
{% include '@global/header.twig' %}
```

If the template doesn't exist in that namespace, Craft throws a `TemplateNotFound` exception.

The `@` is the configurable [`prefix`](/guide/settings/), and only namespaces with a `handle` can be addressed this way.
