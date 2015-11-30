# eZ Studio

## What is eZ Studio ?
*eZ Studio* is a commercial CMS (Content Management System) software developed by eZ Systems AS and the eZ Community.

*eZ Studio* is a standalone product which is based on *eZ Platform*, it is independent, as a set of bundles. eZ Studio, like Platform is built on top of the Symfony framework (Full Stack). It has been in development since 2014.

## eZ Studio vs. eZ Platform
This repository contains *eZ Studio* the next generation CMS which uses the same Symfony kernel as *eZ Platform* (formerly known also as *eZ Publish 6*), but does not include the legacy kernel, nor its library dependencies.
eZ Studio is where the new features are added.

### Abstract:
- **Very extensible** *You can extend the application and the content model in many ways*
- **Future & backwards compatible** *Strong BC policy on data as well as code*
- **Multi channel by design** *Strong focus on separation between <sup>semantic</sup> content & design*
- **Scalable** *Easily scale across multiple servers out of the box*
- **Future proof** *Architecture designed to allow even more content scalability and performance in the future*
- **Stable** *Built on experience building CMS since early 2000, and in production since 2014*
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
*eZ Studio* is a commercial software which adds advanced features for editorial teams and media companies, 100% built on top of *eZ Platform* APIs.

- [eZ Studio Architecture, including "Platform Stack"](https://doc.ez.no/pages/viewpage.action?pageId=11403666)
- [eZ Studio and eZ Platform RoadMap](http://ez.no/Blog/What-to-Expect-from-eZ-Studio-and-eZ-Platform)
- [eZ Platform and Studio 2015 release plan](http://ez.no/Blog/What-Releases-to-Expect-from-eZ-in-2015)
- [eZ Platform and Studio beta versions](http://ez.no/Blog/Introducing-the-beta-of-eZ-s-next-generation-software)
- eZ Systems AS *(commercial products and services)*: [ez.no](http://ez.no/)
- eZ Community: [share.ez.no](http://ez.no/)

http://ez.no/Blog/What-to-Expect-from-eZ-Studio-and-eZ-Platform

## Install and Upgrade
For installation & upgrade instructions, see [INSTALL.md](https://github.com/ezsystems/ezstudio/blob/master/INSTALL.md).

## Requirements
**eZ Studio** currently has the same requirements as *eZ Platform*, further details on the [5.4 requirements](https://doc.ez.no/display/EZP/Requirements+5.4) page.

*TL;DR: minimum PHP 5.4.4 and higher, using mod_php or php-fpm.*

## Issue tracker
Submitting bugs, improvements and stories is possible on https://jira.ez.no/browse/EZS.
If you discover a security issue, please see how to responsibly report such issues on https://doc.ez.no/Security.

Unlike eZ Publish Platform 5.x, eZ Studio does not ship with eZ Publish Legacy (4.x). For Platform kernel use combined
with legacy, eZ Publish Platform 5.4 is the most stable choice, offering support and maintenance updates until 2021.
Alternative is releases of eZ Publish Community, latest as of Oct 2015 is v2014.11 found at
[ezpublish-community](https://github.com/ezsystems/ezpublish-community).

## COPYRIGHT
Copyright (C) 1999-2015 eZ Systems AS. All rights reserved.

## LICENSE
http://ez.no/Products/About-our-Software/Licenses-and-agreements/eZ-Business-Use-License-Agreement-eZ-BUL-Version-2.1 eZ Business Use License Agreement eZ BUL Version 2.1
