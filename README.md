# eZ Publish 5

## What is eZ Publish?
**eZ Publish 5** is a professional PHP CMS (content management system).

It is database, platform and browser independent. Because it is
browser based it can be used and updated from anywhere as long as you have
access to the Internet.


## Architecture
### Public API
**eZ Publish 5** relies on a flexible, layered, service oriented API.
It exposes clear, content oriented domain objects.

### HMVC
eZ Publish 5 is built on top of **[Symfony2](http://symfony.com)** full stack framework, taking advantage of
every components provided, giving all its **Hierarchical Model View Controller** (aka *HMVC*) power.

### Chained routing
A chain router is introduced, allowing to take advantage of declared routes in your `routing.yml` config file as well as
URL aliases to match content (aka *dynamic routing*), or routing fallback to the old eZ Publish 4 modules.

### Template engine
The default template engine used by the system is **[Twig](http://twig.sensiolabs.org/)**.
**Twig** is a modern, powerful and easy to extend template engine.

> As Symfony2 allows usage of multiple template engines, it is also possible to do so in eZ Publish 5, but all the
> content oriented functionnalities are only available with Twig.


## Backwards compatibility
eZ Publish 5 is **100% data compatible** with version 4 (the same database can be used).

### Legacy template inclusion
It is possible to include templates from the *legacy kernel* (`*.tpl`) in new twig templates:

```jinja
{% ez_legacy_include "design:my/old_template.tpl" with {"someVar": "someValue"} %}
```

### Fallback routing
A fallback mechanism is provided so that your modules built on top of eZ Publish 4 (aka *Legacy kernel*)
can still be used in the new architecture.


## LICENCE
eZ Systems AS & GPL v2.0

## INSTALL
For installation instructions, see [INSTALL.md](https://github.com/ezsystems/ezpublish5/blob/master/INSTALL.md).
