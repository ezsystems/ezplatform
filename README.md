# eZ Platform

[![Build Status](https://img.shields.io/travis/ezsystems/ezplatform.svg?style=flat-square)](https://travis-ci.org/ezsystems/ezplatform)
[![Downloads](https://img.shields.io/packagist/dt/ezsystems/ezplatform.svg?style=flat-square)](https://packagist.org/packages/ezsystems/ezplatform)
[![Latest release](https://img.shields.io/github/release/ezsystems/ezplatform.svg?style=flat-square)](https://github.com/ezsystems/ezplatform/releases)
[![License](https://img.shields.io/packagist/l/ezsystems/ezplatform.svg?style=flat-square)](LICENSE)

## What is eZ Platform?
*eZ Platform* is a fully open source professional CMS (Content Management System) developed by eZ Systems and the eZ Community.

Current *eZ Platform v2* is the 7th generation of *eZ Publish*, it is built on top of the Symfony 3.4LTS framework (Full Stack).
It has been in development since 2011, and integral part of the *eZ Publish Platform 5.x* as "Platform stack" since 2012.

#### Abstract:

- **Very extensible** — You can extend the application and the content model in many ways.
- **Future and backwards compatible** — Strong backward compatibility policy on data as well as code.
- **Multi-channel by design** — Strong focus on separation between <sup>semantic</sup> content and design.
- **Scalable** — Easily scale across multiple servers out of the box.
- **Future proof** — Uses architecture designed to allow even more content scalability and performance in the future.
- **Stable** — Built on experience in building CMS that has been gathered since early 2000.
- **Integration friendly** — Numerous events and signals to hook into for advanced needs.

#### Further information:

eZ Platform is fully open source and it is the foundation for the commercial *eZ Platform Enterprise Edition* software, which adds advanced features for editorial teams, entirely built on top of *eZ Platform* APIs.

- eZ Platform Developer Hub: [ezplatform.com](https://ezplatform.com/)
- [eZ Platform Open Source and Enterprise Edition roadmap](http://doc.ez.no/roadmap)
- eZ Systems (commercial products and services): [ez.no](https://ez.no/)


## Installation

**Note:** For simplified installation, consider using community-supported [eZ Launchpad](https://ezsystems.github.io/launchpad/) which takes care of the whole server setup for you.

Installation instructions below are for installing a clean installation of eZ Platform in latest version with _no_ demo content or demo website.
Full installation documentation is [in the online docs](https://doc.ezplatform.com/en/latest/getting_started/install_using_composer/).
It includes instructions on installing other distributions _(like [ezplatform-ee](https://github.com/ezsystems/ezplatform-ee) enterprise edition)_, or other versions.

#### Prerequisites

These instructions assume you have already installed:

- PHP _(7.1 or higher)_
- Web Server _(Recommeneded: Apache / Nginx. Use of PHP's built-in development server is also possible)_
- Database server _(MySQL 5.5+ or MariaDB 10.0+)_
- [Composer](https://doc.ezplatform.com/en/latest/getting_started/about_composer/)
- Git _(for development)_

For more details on requirements, see [online documentation](https://doc.ezplatform.com/en/latest/getting_started/requirements_and_system_configuration/).


#### Install eZ Platform _(clean distribution)_

Assuming you have prerequisites sorted out, you can get the install up and running with the following commands in your terminal:

``` bash
composer create-project --keep-vcs ezsystems/ezplatform ezplatform ^2
cd ezplatform
```

**Note:** If composer is installed locally instead of globally, the first command will start with `php composer.phar`.

During the installation process you will be asked to provide database host name, login, password, etc.
The configuration details will be placed in `<ezplatform>/app/config/parameters.yml`.

Next you will receive instructions on how to install data into the database, and how to run a simplified dev server using the `bin/console server:run` command.

**Tip:** For a more complete and better performing setup using Apache or Nginx, see how to [install eZ Platform manually](https://doc.ezplatform.com/en/latest/getting_started/install_manually/).

## Issue tracker
Submitting bugs, improvements and stories is possible on [https://jira.ez.no/browse/EZP](https://jira.ez.no/browse/EZP).
If you discover a security issue, please see how to responsibly report such issues in ["Reporting security issues in eZ Systems products"](https://doc.ezplatform.com/en/latest/guide/reporting_issues/#reporting-security-issues-in-ez-systems-products).

## Backwards compatibility
eZ Platform aims to be **fully content compatible** with eZ Publish 5.x, meaning that the content in these versions of the CMS can be upgraded using
[online documentation](https://doc.ezplatform.com/en/latest/migrating/migrating_from_ez_publish_platform/) to eZ Platform.

Unlike eZ Publish Platform 5.x, eZ Platform does not ship with eZ Publish Legacy (4.x). But this is available by optional installing [LegacyBridge](https://github.com/ezsystems/LegacyBridge/releases/) to allow eZ Platform and eZ Publish Legacy to run together, this is only recommended for migration use cases and not for new installations.

## COPYRIGHT
Copyright (C) 1999-2020 eZ Systems AS. All rights reserved.

## LICENSE
http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
