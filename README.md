# eZ Publish 5

## What is eZ Publish?
**eZ Publish 5** is a professional PHP CMS (content management system).

It strives to be database, platform and browser independent. Because it is
browser based it can be used from anywhere, on any device, as long as you have
access to the Internet. One of it's unique features is how you can model
content without having to change your database. It allows you to effectively
define model structures using fields, trees and relations, and has a very
flexible permission system which allows you to define who has access to
perform actions under which limiting factors.

eZ Publish exists in two versions; this, the community version is available under
the GPLv2 license, while several extended versions for enterprise & business is available
under a more permissive business license, see [ez.no](http://ez.no/) for more info.

## Install, Upgrade and Getting started
For installation & upgrade instructions, see [INSTALL.md](https://github.com/ezsystems/ezpublish-community/blob/master/INSTALL.md).

To get started with coding, see [GETTING_STARTED.md](https://github.com/ezsystems/ezpublish-community/blob/master/GETTING_STARTED.md).

## Requirements
**eZ Publish 5** has the same requirements as [Symfony2](http://symfony.com/doc/master/reference/requirements.html),
plus the [regular eZ Publish 4 ones](http://doc.ez.no/eZ-Publish/Technical-manual/4.x/Installation/Normal-installation/Requirements-for-doing-a-normal-installation).

Minimum PHP version is 5.3.17, but 5.5.x is recommended.

## Backwards compatibility
eZ Publish 5 is **100% data compatible** with version 4, as in the same
database can be used by following the [normal](http://doc.ez.no/eZ-Publish/Upgrading) upgrade path.

## Architecture

### Public API
**eZ Publish 5** relies on a flexible, layered, service oriented API.
The Public API consists of the Model (the M in MVC) and all
apis related to operations available for this Model. More info can be found
in /vendor/ezsystems/ezpublish-kernel/Readme.md after installation.

### MVC
eZ Publish 5 is built on top of **[Symfony2](http://symfony.com)** full stack framework, taking advantage of
every component provided, including all its **Hierarchical Model View Controller** (aka *HMVC*) power.

### Chained routing
A chain router is introduced, allowing to take advantage of declared routes in the `routing.yml` config file as well as
URL aliases to match content (aka *dynamic routing*), or routing fallback to the old eZ Publish 4 modules.

### Template engine
The default template engine used by the system is **[Twig](http://twig.sensiolabs.org/)**.
**Twig** is a modern, powerful and easy to extend template engine.

> As Symfony2 allows usage of multiple template engines, it is also possible to do so in eZ Publish 5, but all the
> content oriented functionality are only available with Twig.


## COPYRIGHT
Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.

## LICENSE
http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
