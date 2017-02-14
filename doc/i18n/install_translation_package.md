# Install new translation package

## Introduction

This documentation is for eZ Platform integrators who realize projects based on eZ Platform. It will present
how to install a new package of translations in your project.

## Translation packages per language

To allow users to install only what they need, we have split every language into a dedicated package.

All translation packages are published on [ezplatform-i18n organisation on github][ezplatform-i18n-org]

**Important**: these packages are read only, they must be updated with the [eZ Platform i18n git split command][ezplatform-i18n].

## Install a new language in your project

If you want to install a new language in your project, you just have to install the corresponding package.

For example, if you want to translate your application into Portuguese (pt_PT: the only package supported by our QA team ;)),
you just have to run:

    composer require ezplatform-i18n/ezplatform-i18n-pt_pt

and then clear the cache.

Now you can reload your eZ Platform administration page which will be translated in Portuguese (if your browser is
configured to pt_PT.)

[crowdin-ezplatform]: https://crowdin.com/project/ezplatform
[ezplatform-i18n-org]: https://github.com/ezplatform-i18n
[ezplatform-i18n]: https://github.com/ezsystems/ezplatform-i18n
