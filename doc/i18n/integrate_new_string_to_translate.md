# Integrate new strings to translate

## Introduction

This documentation is for the eZ Systems team and presents the process of sending the new strings for translation.

To be able to provide a single translation project on our translator platform, we gathered all translation
files in a single repository [ezplatform-i18n][ezplatform-i18n] which is synchronized with [Crowdin][crowdin-ezplatform].

## Add new strings in ezplatform-i18n

Before extracting new strings you have to make sure that your eZ Systems bundles are up to date on your eZ Platorm installation.

Then your project should be in dev mode with development dependencies installed and ezsystems/ezplatform-i18n up to date.

To synchronize [ezplatform-i18n][ezplatform-i18n] with new translations, we implemented a script which extracts translation files from
ezsystems bundles and formats them to the Crowdin source file format.

You just have to run this command from the eZ Platform project:

    sh bin/synchronize-translations.sh

Then make a PR on the [ezplatform-i18n][ezplatform-i18n] repository.

**Important:** you'll notice that all files will be updated at least to change the date attribute. If this is the
only change to a file, please don't commit it.

## Add new strings for translation

At this point, the only thing you have to do to add the new strings in Crowdin is to merge the PR and make sure that
the destination branch is configured as a 'branch for translation' on [Crowdin][crowdin-github-integration].

**Note:** you'll notice Crowdin will make a PR to update ach_UG file corresponding to your modification.

## Next

You should wait for the translators' work and read the [Distribute translations documentation][distribute-translations]

[crowdin-ezplatform]: https://crowdin.com/project/ezplatform
[crowdin-github-integration]: https://crowdin.com/project/ezplatform/settings#integration
[ezplatform-i18n-org]: https://github.com/ezplatform-i18n
[ezplatform-i18n]: https://github.com/ezsystems/ezplatform-i18n
[distribute-translations]: /doc/i18n/distribute_translations.md
