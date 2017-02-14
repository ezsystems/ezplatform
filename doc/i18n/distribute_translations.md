# Distribute translations

## Introduction

This documentation is for the eZ Systems team and presents the process of integrating new translations and distributing
them as a package.

## Merge Crowdin contributions

When translators contribute to translate the strings you added, [Crowdin][crowdin-ezplatform] will make a huge PR on the
l10n_master branch (or l10n_xx where xx is the target branch).

You can squash and merge this PR directly on GitHub with the following rules:

- **Squash commits**: Crowdin does a lot of commits so this is mandatory.
- **Update commit message**: Add an understandable message with the locales translated...
- **DO NOT remove the branch**: Crowdin synchronization is based on it, so if you remove it you break the Crowdin workflow.

Then when contributions are merged on the target branch, you just need to synchronize the translation packages.

## Translation packages per language

To allow users to install only what they need, we have split every language into a dedicated package.

All translation packages are published on [ezplatform-i18n organisation on github][ezplatform-i18n-org]

**Important**: these packages are read only, they must be updated with the [eZ Platform i18n git split command][ezplatform-i18n].

## Next

The last step to validate that the translation has been correctly added is to [install the corresponding translation package][install-translation].

[crowdin-ezplatform]: https://crowdin.com/project/ezplatform
[ezplatform-i18n-org]: https://github.com/ezplatform-i18n
[ezplatform-i18n]: https://github.com/ezsystems/ezplatform-i18n
[install-translation]: /doc/i18n/install_translation_package.md
