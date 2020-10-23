# Ibexa Platform

[![Build Status](https://img.shields.io/travis/ezsystems/ezplatform.svg?style=flat-square)](https://travis-ci.org/ezsystems/ezplatform)
[![Downloads](https://img.shields.io/packagist/dt/ezsystems/ezplatform.svg?style=flat-square)](https://packagist.org/packages/ezsystems/ezplatform)
[![Latest release](https://img.shields.io/github/release/ezsystems/ezplatform.svg?style=flat-square)](https://github.com/ezsystems/ezplatform/releases)
[![License](https://img.shields.io/packagist/l/ezsystems/ezplatform.svg?style=flat-square)](LICENSE)

## What is Ibexa Platform?
*Ibexa Platform* is a fully open source professional CMS (Content Management System) developed by Ibexa and the Ibexa Community.

Current *Ibexa Platform v3* is built on top of the Symfony 5 framework (Full Stack).

#### Abstract:

- **Very extensible** — You can extend the application and the content model in many ways.
- **Future and backwards compatible** — Strong backward compatibility policy on data as well as code.
- **Multi-channel by design** — Strong focus on separation between <sup>semantic</sup> content and design.
- **Scalable** — Easily scale across multiple servers out of the box.
- **Future proof** — Uses architecture designed to allow even more content scalability and performance in the future.
- **Stable** — Built on experience in building CMS that has been gathered since early 2000.
- **Integration friendly** — Numerous events and signals to hook into for advanced needs.

#### Further information:

Ibexa Platform is fully open source and it is the foundation for the commercial *Ibexa Digital Experience Platform* software, which adds advanced features for editorial teams, entirely built on top of *Ibexa Platform* APIs.

- [Ibexa products roadmap](https://portal.productboard.com/ibexa/1-ibexa-dxp)
- Ibexa (commercial products and services): [ibexa.co](https://ibexa.co/)


## Installation

Installation instructions below are for installing a clean installation of Ibexa Platform in latest version with _no_ demo content or demo website.
Full installation documentation is [in the online docs](https://doc.ibexa.co/en/latest/getting_started/install_ez_platform/).
It includes instructions on installing other products _(like [Ibexa Experience](https://github.com/ezsystems/ezplatform-ee))_, or other versions.

#### Prerequisites

These instructions assume you have already installed:

- PHP _(7.3 or higher)_
- Web Server _(Recommended: Apache / Nginx. Use of PHP's built-in development server is also possible)_
- Database server _(MySQL 5.5+ or MariaDB 10.0+)_
- [Composer](https://doc.ibexa.co/en/latest/getting_started/install_ez_platform/#get-composer)
- Git _(for development)_

For more details on requirements, see [online documentation](https://doc.ibexa.co/en/latest/getting_started/requirements/).


#### Install Ibexa Platform _(clean distribution)_

Assuming you have prerequisites sorted out, you can get the install up and running with the following commands in your terminal:

``` bash
composer create-project --keep-vcs ezsystems/ezplatform ezplatform ^3
cd ezplatform
```

**Note:** If composer is installed locally instead of globally, the first command will start with `php composer.phar`.

You must add your database connection credentials (hostname, login, password) to the environment file.  
To do this, in the main project directory, the `.env` file, change the parameters that are prefixed with `DATABASE_` as necessary.
Store the database credentials in your `.env.local` file. Do not commit the file to the Version Control System.

Use the following command to install Ibexa Platform (insert base data into the database):

```bash
composer ezplatform-install
```

**Tip:** For a more complete and better performing setup using Apache or Nginx, see how to [install Ibexa Platform manually](https://doc.ibexa.co/en/latest/getting_started/install_ez_platform/).

## Issue tracker
Submitting bugs, improvements and stories is possible on [https://jira.ez.no/browse/EZP](https://jira.ez.no/browse/EZP).
If you discover a security issue, please see how to responsibly report such issues in ["Reporting security issues in Ibexa products"](https://doc.ibexa.co/en/latest/guide/reporting_issues/#reporting-security-issues-in-ez-systems-products).

## Backwards compatibility
Ibexa Platform aims to be **fully content compatible** with eZ Publish 5.x, meaning that the content in these versions of the CMS can be upgraded using
[online documentation](https://doc.ezplatform.com/en/latest/migrating/migrating_from_ez_publish_platform/) to Ibexa Platform.


## COPYRIGHT
Copyright (C) 1999-2020 Ibexa AS. All rights reserved.

## LICENSE
http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
