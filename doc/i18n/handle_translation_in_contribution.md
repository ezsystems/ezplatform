# Translation : How to integrate new string in your contributions

## Introduction

This documentation is made for contributors to eZ Platform. It will explain you how to format your contribution
to fit in the translation workflow.

## How to add new string to be translated

To ease translator work we use JMSTranslation which allow us to add a desc parameter to translated strings.
This desc should contain the default english string and will be displayed to translator as a help message on Crowdin.

For example in a twig template this will look like this:

    {{ 'field_definition.description'|trans|desc("Description") }} 

you can even use this feature with transchoice:

    {{ 'yes_no'|transchoice(field_definition.isRequired)|desc("{0} No|{1} Yes") }}

## How to extract new string from template

Every translation are handled by repository. So if you add new strings in ezpublish-kernel, you will extract them into
ezpublish-kernel translation files.

To help you on this task and maintain consistency, we implement a dedicated script.

    bin/extract-translations.sh
    
**Important:** you'll notice that all files will be updated at least to change the date attribute. If this is the
only change to a file, please don't commit it.

**Note:** this script is present in every eZ Systems repositories which handle translation, so you can use the same command
on platform-ui-bundle or repository-forms.

## Next what happens

A this point, your contributor job is done. When you PR will be merged, eZ System team will add your new string in the 
[ezplatform-i18n][ezplatform-i18n] repository so they can be translated.

[crowdin-ezplatform]: https://crowdin.com/project/ezplatform
[ezplatform-i18n-org]: https://github.com/ezplatform-i18n
[ezplatform-i18n]: https://github.com/ezsystems/ezplatform-i18n
