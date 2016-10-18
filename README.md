# eZ Studio

## What is eZ Studio ?
*eZ Studio* is commercial CMS (Content Management System) software developed by eZ Systems.

*eZ Studio* derives from *eZ Platform*. It is composed of a set of bundles. eZ Studio, like eZ Platform, is built on top of the Symfony framework (Full Stack). It has been in development since 2014.

### How to get access to eZ Studio?

While this meta repository, `ezstudio`, is public to ease installation and upgrades, full access can be obtained in one of three ways:
- Request an online demo on [ezstudio.com](http://ezstudio.com/)
- As a partner, download trial version from [Partner Portal](http://ez.no/Partner-Portal)
- As a customer with an eZ Enterprise subscription, get full version from [Service Portal](https://support.ez.no/Downloads).
  Or by setting up [Composer Authentication Tokens](https://doc.ez.no/display/DEVELOPER/Using+Composer) for use in combination with this repository.

## eZ Studio vs. eZ Platform
[eZ Studio](http://ezstudio.com/) is a distribution flavor of [eZ Platform](http://ezplatform.com/), the next generation CMS which uses the same Symfony-based kernel as *eZ Platform* (formerly known also as *eZ Publish 6*).
In short, eZ Studio comes with new features and services that extend eZ Platform functionality for media industry and team collaboration.

### Abstract:
- **Very extensible** *You can extend the application and the content model in many ways*
- **Future & backwards compatible** *Strong BC policy on data as well as code*
- **Multi channel by design** *Strong focus on separation between <sup>semantic</sup> content & design*
- **Scalable** *Easily scale across multiple servers out of the box*
- **Future proof** *Architecture designed to allow even more content scalability and performance in the future*
- **Stable** *Built on experience in customizing and extending the highly flexible CMS solutions since early 2000, and in production since 2014*
- **Integration friendly** *Numerous events and signals to hook into for advanced workflow needs*

### Main packages:
- **ezpublish-kernel** (building on top of **Symfony Framework**):
 - Content Repository with a highly flexible content model exposed via a Public API.<br>
   Out of the box powered by SQL *Storage Engine* using [Doctrine DBAL](http://doctrine-dbal.readthedocs.org/en/latest/reference/configuration.html#driver),
   data cache implementation using [Stash](http://www.stashphp.com/Drivers.html) and binary file system handled by [Flysystem](https://github.com/thephpleague/flysystem#adapters).
   Improved *Storage Engine* planned, custom implementation for increased data scalability already possible.
 - Powerful (& extensible) Content Query engine, currently powered by SQL, soon Solr/ElasticSearch
 - Very high performance thanks to content & user aware HTTP <sup>"view"</sup> cache (now [using](https://github.com/FriendsOfSymfony/FOSHttpCacheBundle))
 - Introduces concept of "web sites" allowing you to manage several within one installation
 - Allows to rapidly set up *Contextual override* of content display twig templates as well as controller based on Content type, section, and much more.
 - Helpers, services, events and signals allowing you to efficiently create everything from simple web sites to complex applications
- **PlatformUIBundle**: A modern, extensible "backend" UI for managing content & administering the site

### Further information:
*eZ Studio* is commercial software which adds advanced features for editorial teams and media companies, 100% built on top of *eZ Platform* APIs.

- [eZ Studio Architecture, including "Platform Stack"](https://doc.ez.no/display/DEVELOPER/Architecture:+An+Open+Source+PHP+CMS+Built+On+Symfony2+Full+Stack)
- eZ Systems *(commercial products and services)*: [ez.no](http://ez.no)
- eZ Community: [share.ez.no](http://ez.no)
- eZ Platform Developer Hub: [ezplatform.com](https://ezplatform.com/)
- [eZ Studio and eZ Platform RoadMap](http://doc.ez.no/roadmap)

http://ez.no/Blog/What-to-Expect-from-eZ-Studio-and-eZ-Platform

## Install
For manual installation instructions, see [INSTALL.md](https://github.com/ezsystems/ezstudio/blob/master/INSTALL.md).
For simplified installation, see our Docker Tools Beta instructions in [doc/docker-compose/README.md](https://github.com/ezsystems/ezstudio/blob/master/doc/docker-compose/README.md).

## Requirements
Full requirements can be found on the [Requirements](https://doc.ez.no/pages/viewpage.action?pageId=31429536) page.

*TL;DR: supported PHP versions are 5.5, 5.6 and 7.0 (for dev use), using mod_php or php-fpm, and either MySQL 5.5/5.6 or MariaDB 5.5/10.0.*

## Issue tracker
Submitting bugs, improvements and stories is possible on https://jira.ez.no/browse/EZS.
If you discover a security issue, please see how to responsibly report such issues on https://doc.ez.no/Security.

## Running BDD
For instruction on how to run the functional tests, see [RUNNING_BEHAT.md](https://github.com/ezsystems/ezplatform/blob/master/RUNNING_BEHAT.md).

## Backwards compatibility
eZ Platform aims to be **100% content compatible** with eZ Publish 5.x, 4.x and 3.x *(introduced in 2002)*, meaning that content in those versions of the CMS can be upgraded using
[online documentation](http://doc.ez.no/eZ-Publish/Upgrading) to eZ Platform.

Unlike eZ Publish Platform 5.x, eZ Studio does not ship with eZ Publish Legacy (4.x). For Platform kernel use combined
with legacy, eZ Publish Platform 5.4 is the most stable choice, offering support and maintenance updates until 2021.
Alternative is releases of eZ Publish Community, latest as of Oct 2015 is v2014.11 found at
[ezpublish-community](https://github.com/ezsystems/ezpublish-community).

## COPYRIGHT
Copyright (C) 1999-2016 eZ Systems AS. All rights reserved.

## LICENSE
http://ez.no/Products/About-our-Software/Licenses-and-agreements/eZ-Business-Use-License-Agreement-eZ-BUL-Version-2.1 eZ Business Use License Agreement eZ BUL Version 2.1
