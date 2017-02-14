# Translation: How to integrate new strings in your contributions

## Introduction

This documentation is for contributors to eZ Platform. It will explain how to format your contribution
to fit in the translation workflow.

## How to add new strings to be translated

To ease translators' work we use JMSTranslation which allows us to add a `desc` parameter to translated strings.
This `desc` should contain the default English string and will be displayed to the translator as a help message on Crowdin.

For example in a twig template it will look like this:

    {{ 'field_definition.description'|trans|desc("Description") }}

you can even use this feature with `transchoice`:

    {{ 'yes_no'|transchoice(field_definition.isRequired)|desc("{0} No|{1} Yes") }}

## How to extract new strings from template

All translations are handled by the repository. So if you add new strings in ezpublish-kernel, you will extract them into
ezpublish-kernel translation files.

To help you in this task and maintain consistency, we implement a dedicated script.

    bin/extract-translations.sh

**Important:** you'll notice that all files will be updated at least to change the date attribute. If this is the
only change to a file, please don't commit it.

**Note:** this script is present in all eZ Systems repositories which handle translations, so you can use the same command
on platform-ui-bundle or repository-forms.

## What happens next

At this point, your contributor job is done. When your PR is merged, the eZ Systems team will add your new strings in the
[ezplatform-i18n][ezplatform-i18n] repository so they can be translated.

[crowdin-ezplatform]: https://crowdin.com/project/ezplatform
[ezplatform-i18n-org]: https://github.com/ezplatform-i18n
[ezplatform-i18n]: https://github.com/ezsystems/ezplatform-i18n
