# eZ Platform

[![Build Status](https://img.shields.io/travis/ezsystems/ezplatform.svg?style=flat-square)](https://travis-ci.org/ezsystems/ezplatform)
[![Downloads](https://img.shields.io/packagist/dt/ezsystems/ezplatform.svg?style=flat-square)](https://packagist.org/packages/ezsystems/ezplatform)
[![Latest release](https://img.shields.io/github/release/ezsystems/ezplatform.svg?style=flat-square)](https://github.com/ezsystems/ezplatform/releases)
[![License](https://img.shields.io/packagist/l/ezsystems/ezplatform.svg?style=flat-square)](LICENSE)

## What is eZ Platform ?
*eZ Platform* is a 100% open source professional CMS (Content Management System) developed by eZ Systems and the eZ Community.

*eZ Platform* is the 6th generation of *eZ Publish*, it is built on top of the Symfony framework (Full Stack).
It has been in development since 2011, and integral part of the *eZ Publish Platform 5.x* as "Platform stack" since 2012.

## eZ Publish vs. eZ Platform
This repository contains *eZ Platform* (formerly known also as *eZ Publish 6*) the next generation cms which uses the same Symfony kernel as *eZ Publish 5*, but does not include the legacy kernel, nor its library dependencies.
eZ Platform is where the new features are added.

The [eZ Publish 5](https://github.com/ezsystems/ezpublish-community) repository, mostly maintained for backwards compatibility, integrates eZ Publish Legacy.

### Abstract:
- **Very extensible** *You can extend the application and the content model in many ways*
- **Future & backwards compatible** *Strong BC policy on data as well as code*
- **Multi channel by design** *Strong focus on separation between <sup>semantic</sup> content & design*
- **Scalable** *Easily scale across multiple servers out of the box*
- **Future proof** *Architecture designed to allow even more content scalability and performance in the future*
- **Stable** *Built on experience building CMS since early 2000, and in production since 2012*
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
- **PlatformUIBundle**: A modern, extensible "Backend" UI for managing content & administering the site

### Further information:
eZ Platform is 100% open source and is the foundation for the commercial *eZ Platform Enterprise Edition* software which adds advanced features for editorial teams, 100% built on top of *eZ Platform* APIs.

- eZ Platform Developer Hub: [ezplatform.com](https://ezplatform.com/)
- [eZ Platform Open Source and Enterprise Edition roadmap](http://doc.ez.no/roadmap)
- eZ Systems (commercial products and services): [ez.no](https://ez.no/)

## Install
For manual installation instructions, see [INSTALL.md](https://github.com/ezsystems/ezplatform/blob/master/INSTALL.md).
For simplified installation, rather consider using [eZ Launchpad](https://ezsystems.github.io/launchpad/) which takes care about the whole setup for you.


### eZ Platform Demo
This repository lets you create a clean, empty installation of eZ Platform. This type of installation is used for developing from scratch. You can also choose a version of eZ Platform including a demo, that is an example website. It is available in the following repository: https://github.com/ezsystems/ezplatform-demo

## Requirements
Full requirements can be found on the [Requirements](https://doc.ez.no/display/TECHDOC/Requirements) page.

*TL;DR: supported PHP versions are 5.6, 7.0 and 7.1 (for dev use), using mod_php or php-fpm, and either MySQL 5.5/5.6 or MariaDB 10.0/10.1.*

## Issue tracker
Submitting bugs, improvements and stories is possible on https://jira.ez.no/browse/EZP.
If you discover a security issue, please see how to responsibly report such issues on https://doc.ez.no/Security.

## Running BDD
For instruction on how to run the functional tests, see [RUNNING_BEHAT.md](https://github.com/ezsystems/ezplatform/blob/master/RUNNING_BEHAT.md).

## Backwards compatibility
eZ Platform aims to be **100% content compatible** with eZ Publish 5.x, 4.x and 3.x *(introduced in 2002)*, meaning that content in those versions of the CMS can be upgraded using
[online documentation](http://doc.ez.no/eZ-Publish/Upgrading) to eZ Platform.

Unlike eZ Publish Platform 5.x, eZ Platform does not ship with eZ Publish Legacy (4.x). For Platform kernel use combined with legacy, eZ Publish Platform 5.4 is the most stable choice, offering support and maintenance updates until 2021.

An alternative is releases of eZ Publish Community; the latest as of Oct 2015 is v2014.11, found at
[ezpublish-community](https://github.com/ezsystems/ezpublish-community).

You can also consider using eZ Platform [LegacyBridge](https://github.com/ezsystems/LegacyBridge/releases/) to allow eZ Platform and eZ Publish Legacy to run together.

## COPYRIGHT
Copyright (C) 1999-2016 eZ Systems AS. All rights reserved.

## LICENSE
http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
