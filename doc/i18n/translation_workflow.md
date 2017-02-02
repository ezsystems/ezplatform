# Translation workflow

## Introduction

eZ Platform is translated in several languages with the help of [Crowdin][crowdin-ezplatform].

To organize contribution and allow users to install only what they need, we set up a workflow
based on three steps.

##  eZ Platform i18n: Main translation repository

To be able to provide a single translation project on our translator platform, we gathered all translation
files in a single repository which is synchronized with Crowdin.

Translators can contribute on [Crowdin][crowdin-ezplatform] and every contribution is merged into [ezplatform-i18n repository][ezplatform-i18n].

**Important**: This repository is not supposed to be used on a production project, every translation is
split into a dedicated package.

### Translation extraction

To ease the extraction of translation strings, we implemented a script in each bundle which you can run with this command:

    sh bin/extract-translations.sh
    
This script will update files in ezplatform-i18n repository.

**Important**: you'll notice that all files will be updated at least to change the date attribute. If this is the
only change to a file, please don't commit it.

Then push your PR on github, you'll notice Crowdin will make a PR to update ach_UG file corresponding to your modification.

### Merge Crowdin contributions

When translators contribute to translate the strings you added, Crowdin will make a huge PR on the l10n_master branch
(or l10n_xx where xx is the target branch).

You can squash and merge this PR directly on github with the following rules:

- **Squash commits**: Crowdin does a lot of commits so this is mandatory
- **Update commit message**: Add an understandable message with the locales translated...
- **DO NOT remove the branch**: Crowdin synchronization is based on it, so if you remove it you break the Crowdin workflow.

Then when contributions are merged on the target branch, you just need to synchronize the translation packages.

### Translation synchronization

To synchronize eZ Platform i18n with new translations, we implemented a script which extracts translation files from
ezsystem bundles and formats them to the Crowdin source file format.

You just have to run this command from eZ Platform project:

    sh bin/synchronize-translations.sh
    
Then commit and push eZ Platform i18n.

## Translation packages per language

To allow users to install only what they need, we have split every language into a dedicated package.

All translation packages are published on [ezplatform-i18n organisation on github][ezplatform-i18n-org]

**Important**: these packages are read only, they must be updated with the [eZ Platform i18n git split command][ezplatform-i18n].

## Install a new language on your project

If you want to install a new language in your project, you just have to install the corresponding package.

For example, if you want to translate your application into Portuguese (pt_PT: the only package supported by our QA team ;)),
you just have to run:

    composer require ezplatform-i18n/ezplatform-i18n-pt_pt
    
and then clean the cache.

[crowdin-ezplatform]: https://crowdin.com/project/ezplatform
[ezplatform-i18n-org]: https://github.com/ezplatform-i18n
[ezplatform-i18n]: https://github.com/ezsystems/ezplatform-i18n
