# Hacking on eZ Publish 5

eZ Publish 5 is built on top of **Symfony2 full stack framework** (version **2.x**), and as such all guidelines,
requirements and best practices remain the same.

The best way to kickstart is to read the [Symfony2 documentation](http://symfony.com/doc/current/book/page_creation.html)
in order to get the basics.

## Demo bundle
> "Bundle" is the name used for an extension in Symfony.

A demo bundle, [EzDemoBundle](https://github.com/ezsystems/ezpublish-community/tree/master/src/EzSystems/DemoBundle), is provided
in the *src/* directory under the *EzSystems* namespace and, among others, provides implementation for the demo design.
This demo bundle already exposes [some routes](https://github.com/ezsystems/ezpublish-community/blob/master/src/EzSystems/DemoBundle/Resources/config/routing.yml)
allowing to make some tests and hacking.

The most interesting routes for a start are :

- **eZTest**: Loads a content via the Public API and displays it. This content is expected to be a very simple folder with
  *name* and *description* Field Definitions (formerly *content class attributes*).
- **eZTestWithLegacy**: Includes a legacy template in a new one.

> Warning: Public API still supports a limited number of Field Types (formerly *datatypes*), and as such you will probably get exceptions
> regarding that.
>
> To be able to show some content, please create a simple Content Type (formerly *content class*) via the admin interface
> (you can access it from your eZ Publish 5 installation like you already did before).

## Guidelines and features available
### Generating a bundle
eZ Publish 5 comes with [SensioGeneratorBundle](http://symfony.com/doc/current/bundles/SensioGeneratorBundle/index.html).
This bundle provides useful commands, including one to easily generate a new bundle from command line:

```bash
php ezpublish/console generate:bundle
```

Please note that `yml` is the preferred format for configuration.

For more information, [check the documentation for this command](http://symfony.com/doc/current/bundles/SensioGeneratorBundle/index.html).

> Note: By choosing *yes* to *generate the whole directory structure*, you will have a complete bundle, including the *DependencyInjection*
> part and a directory for tests.
>
> Thus this is the recommended way of doing.

### Routing
Any route that is not declared in eZ Publish 5 in an included `routing.yml` and that is not a valid *UrlAlias* will automatically fallback
to eZ Publish legacy (including admin interface).

This allows your old modules to work as before out-of-the-box.

### Developing a controller
When developing a controller (formerly *module*), make sure to extend `eZ\Bundle\EzPublishCoreBundle\Controller` instead of the default Symfony one.
This will allow you to take advantage of additional eZ-specific features (like easier Public API access).

Inside an eZ Controller, you can access to the public API by getting the Repository through the `$this->getRepository()` method.

```php
<?php
namespace My\TestBundle\Controller;

use eZ\Bundle\EzPublishCoreBundle\Controller as EzController;

class MyController extends EzController
{
    public function testAction()
    {
        $repository = $this->getRepository();
        $myContent = $repository->getContentService()->loadContent( 123 );

        return $this->render(
            'TestBundle::test.html.twig',
            array( 'content' => $myContent )
        );
    }
}
```

### Content fields display
Display your content fields (formerly *content object attributes*) through the `ez_render_field()` Twig helper.
This will render it using a template (only the internal one for now) and inject metadata in the markup if in edit mode.

```jinja
{# TestBundle::test.html.twig #}
{# Assuming that a "content" variable has been exposed and that it's an object returned by API #}
{{ ez_render_field( content, 'my_field_identifier' ) }}
```

PHP code corresponding to this helper is located in [Twig ContentExtension](https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Publish/MVC/Templating/Twig/Extension/ContentExtension.php).

Base Twig code can be found in the [base template](https://github.com/ezsystems/ezpublish-kernel/blob/master/eZ/Publish/MVC/Resources/views/Content/content_fields.html.twig).

> **Warning**
>
> Only *ezstring*, *eztext* and raw *ezxmltext* have been implemented to work in this way at the moment.

### Rendering a location
From a Twig template, it is possible to render a content with a sub-request:

```jinja
{% render "ez_content:viewLocation" with {"locationId": 123, "viewMode": "full"} %}
```

### Legacy templates inclusion
It is possible to include old templates (**.tpl*) into new ones:

```jinja
{# Twig template #}
{# Following code will include my/old_template.tpl, exposing $someVar variable in it #}
{% include "design:my/old_template.tpl" with {"someVar": "someValue"} %}
```

> **Note**
>
> Content/Location objects from the Public API are converted into eZContentObject/eZContentObjectTreeNode objects (re-fetched)

### Run legacy PHP code
The new kernel still relies on eZ Publish legacy kernel and runs it when needed inside an isolated PHP closure, making it sandboxed.

It is however still possible to run some PHP code inside that sandbox through the `runCallback()` method.

```php
<?php
// Declare use statements for the classes you may need
use eZINI;

// Inside a controller/action
$settingName = 'MySetting';
$test = array( 'oneValue', 'anotherValue' );
$myLegacySetting = $this->getLegacyKernel()->runCallback(
    function () use ( $settingName, $test )
    {
        // Here you can reuse $settingName and $test variables inside the legacy context
        $ini = eZINI::instance( 'someconfig.ini' );
        return $ini->variable( 'SomeSection', $settingName );
    }
);
```
> `runCallback()` can also take a 2nd argument. Setting to `true` avoids to re-initialize the legacy kernel environment after your call.

