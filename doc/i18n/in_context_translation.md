### In-context UI translation

Since eZ Platform 1.7.0, the interface has been fully translatable. Version 1.8.0 introduces
official support for [crowdin.com](crowdin.com) as a translation management system. In
addition, it integrates support for [in-context translation](in-context), a feature that
allows you to translate strings from the interface, _in context_.

![In-context translation of Platform UI](https://cloud.githubusercontent.com/assets/235928/21649816/44fc2ea0-d2a3-11e6-8c0e-1b5493ea47e9.png)

## Toggling in-context translation
To start translating, you need to set a browser cookie. There are several ways to do this,
but we will highlight a couple here.

### Using the debugging console
One way is to open the development console and run these lines:
- enable: `document.cookie='ez_in_context_translation=1;path=/;'; location.reload();`
- disable: `document.cookie='ez_in_context_translation=;expires=Mon, 05 Jul 2000 00:00:00 GMT;path=/;'; location.reload();`

### Using bookmarks
You can easily create two bookmarks to toggle in-context on/off.

Right-click your browser's bookmark bar and create a new one, with the following label and link:
- Enable in-context: `javascript:(function() {document.cookie='ez_in_context_translation=1;path=/;'; location.reload();})()`
- Disable in-context: `javascript:(function() {document.cookie='ez_in_context_translation=;expires=Mon, 05 Jul 2000 00:00:00 GMT;path=/;'; location.reload();})()`

Then click on the bookmarks from Platform UI to enable/disable in-context.

## Using in-context translation
The first time you enable in-context, if you're not logged in to Crowdin, it will ask you
to log in or register an account. Once done, it will ask you which language you want to
translate to, from the list of languages configured in Crowdin.

Choose your language and you can start translating right away. Translatable strings in the
interface will be outlined in red (untranslated), blue (translated) or green (approved).
When moving over them, an edit button will show up in the top left corner of the outline.
Click on it, and edit the string in the window that shows up.

## Troubleshooting

Make sure you clear your browser's cache in addition to eZ Platform's. Some of the translation resources
use aggressive HTTP cache.

[crowdin.com]: https://crowdin.com
[in-context]: https://support.crowdin.com/in-context-localization/
