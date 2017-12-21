# eZ Platform Enterprise Edition

[![Latest release](https://img.shields.io/github/release/ezsystems/ezplatform-ee.svg?style=flat-square)](https://github.com/ezsystems/ezplatform-ee/releases)
[![License](https://img.shields.io/packagist/l/ezsystems/ezplatform-ee.svg?style=flat-square)](LICENSE)

## What is eZ Platform Enterprise Edition?
*eZ Platform Enterprise Edition* is commercial CMS (Content Management System) software developed by eZ Systems.

*eZ Platform Enterprise Edition* derives from *eZ Platform*. It is composed of a set of bundles. eZ Platform Enterprise Edition, like eZ Platform, is built on top of the Symfony framework (Full Stack). It has been in development since 2014.

### How to get access to eZ Platform Enterprise Edition?

While this meta repository, `ezplatform-ee`, is public to ease installation and upgrades, full access can be obtained in one of three ways:
- Request an online demo on [ez.no](https://ez.no/Products/eZ-Platform-Enterprise-Edition)
- As a partner, download trial version from [Partner Portal](http://ez.no/Partner-Portal)
- As a customer with an eZ Enterprise subscription, get full version from [Service Portal](https://support.ez.no/Downloads).
  Or by setting up [Composer Authentication Tokens](https://doc.ez.no/display/DEVELOPER/Using+Composer) for use in combination with this repository.

## eZ Platform Enterprise Edition vs. eZ Platform
[eZ Platform Enterprise Edition](https://ez.no/Products/eZ-Platform-Enterprise-Edition) is a distribution flavor of [eZ Platform](http://ezplatform.com/), our Symfony-based enterprise level open source CMS.
In short, Enterprise comes with new features and services that extend eZ Platform functionality for media industry and team collaboration.


### Abstract:
- **Very extensible** *You can extend the application and the content model in many ways*
- **Future & backwards compatible** *Strong BC policy on data as well as code*
- **Multi channel by design** *Strong focus on separation between <sup>semantic</sup> content & design*
- **Scalable** *Easily scale across multiple servers out of the box*
- **Future proof** *Architecture designed to allow even more content scalability and performance in the future*
- **Stable** *Built on experience in customizing and extending the highly flexible CMS solutions since early 2000, and in production since 2014*
- **Integration friendly** *Numerous events and signals to hook into for advanced workflow needs*

### Further information:
*eZ Platform Enterprise Edition* is commercial software which adds advanced features for editorial teams and media companies, 100% built on top of *eZ Platform* APIs.

- eZ Platform Developer Hub: [ezplatform.com](https://ezplatform.com/)
- [eZ Platform Open Source and Enterprise Edition roadmap](http://doc.ez.no/roadmap)
- eZ Systems (commercial products and services): [ez.no](https://ez.no/)

## Install
For manual installation instructions, see [INSTALL.md](https://github.com/ezsystems/ezplatform-ee/blob/master/INSTALL.md).
For simplified installation, rather consider using [eZ Launchpad](https://ezsystems.github.io/launchpad/) which takes care about the whole setup for you.

## Requirements
Full requirements can be found on the [Requirements](https://doc.ez.no/pages/viewpage.action?pageId=31429536) page.

*TL;DR: supported PHP versions are 7.1 and up, using php-fpm or mod_php, and either MySQL 5.5/5.6 or MariaDB 10.0/10.1.*

## Issue tracker
Submitting bugs, improvements and stories is possible on https://jira.ez.no/browse/EZEE.
If you discover a security issue, please see how to responsibly report such issues on https://doc.ez.no/Security.

## Backwards compatibility
eZ Platform aims to be **100% content compatible** with eZ Publish 5.x, 4.x and 3.x *(introduced in 2002)*, meaning that content in those versions of the CMS can be upgraded using
[online documentation](http://doc.ez.no/eZ-Publish/Upgrading) to eZ Platform.

Unlike eZ Publish Platform 5.x, eZ Platform does not ship with eZ Publish Legacy (4.x). But this is available by optional installing [LegacyBridge](https://github.com/ezsystems/LegacyBridge/releases/) to allow eZ Platform and eZ Publish Legacy to run together, this is only recommended for migration use cases and not for new installations.

## COPYRIGHT
Copyright (C) 1999-2018 eZ Systems AS. All rights reserved.

## LICENSE
- http://ez.no/Products/About-our-Software/Licenses-and-agreements/eZ-Business-Use-License-Agreement-eZ-BUL-Version-2.1 eZ Business Use License Agreement eZ BUL Version 2.1
- https://ez.no/About-our-Software/Licenses-and-agreements/eZ-Trial-and-Test-License-Agreement-eZ-TTL-v2.0 eZ Trial and Test License Agreement (eZ TTL) v2.0
