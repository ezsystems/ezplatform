# eZ Publish 5

## What is eZ Publish?
**eZ Publish 5** is a professional PHP CMS (content management system).

It is database, platform and browser independent. Because it is
browser based it can be used and updated from anywhere as long as you have
access to the Internet.

## Install, Upgrade and Getting started
For installation & upgrading instructions, see [INSTALL.md](https://github.com/ezsystems/ezpublish5/blob/master/INSTALL.md).

To get started on coding, see [GETTING_STARTED.md](https://github.com/ezsystems/ezpublish5/blob/master/GETTING_STARTED.md).

## Requirements
**eZ Publish 5** meets the same requirements as [Symfony2](http://symfony.com/doc/master/reference/requirements.html),
plus the [regular eZ Publish 4 ones](http://doc.ez.no/eZ-Publish/Technical-manual/4.x/Installation/Normal-installation/Requirements-for-doing-a-normal-installation).

Minimum PHP version is 5.3.3, but 5.4.x is recommended.

## Backwards compatibility
eZ Publish 5 is **100% data compatible** with version 4 (the same database can be used).

## Architecture
### Public API
**eZ Publish 5** relies on a flexible, layered, service oriented API.
Public API consistst of the Model (the M in MVC) in eZ Publish 5 and all
api's related to operations available for thes Models. More info can be found
in /vendor/ezsystems/ezpublish/Readme.md after installation.

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
> content oriented functionality are only available with Twig.

### Legacy template inclusion
It is possible to include templates from the *legacy kernel* (`*.tpl`) in new twig templates:

```jinja
{% ez_legacy_include "design:my/old_template.tpl" with {"someVar": "someValue"} %}
```

### Fallback routing
A fallback mechanism is provided so that your modules built on top of eZ Publish 4 (aka *Legacy kernel*)
can still be used in the new architecture.

## COPYRIGHT
Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.

## LICENCE
http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
