# Getting started on eZ Platform

eZ Platform is built on top of **Symfony2 full stack framework** (version **2.x**), and as such all guidelines,
requirements and best practices remain the same.

The best way to kickstart is to read the [Symfony2 documentation](http://symfony.com/doc/current/book/page_creation.html)
in order to get the basics.

Note: Section below is out-of-date but represent some hints on how to be learn more about contributing to eZ Platform.
      For introduction to *using* eZ Platform, please check our [online doc](doc.ez.no).

## Guidelines and features available
### Generating a bundle
eZ Platform comes with [SensioGeneratorBundle](http://symfony.com/doc/current/bundles/SensioGeneratorBundle/index.html).
This bundle provides useful commands, including one to easily generate a new bundle from command line:

```bash
php app/console generate:bundle
```

Please note that `yml` is the preferred format for configuration.

For more information, [check the documentation for this command](http://symfony.com/doc/current/bundles/SensioGeneratorBundle/index.html).

> Note: By choosing *yes* to *generate the whole directory structure*, you will have a complete bundle, including the *DependencyInjection*
> part and a directory for tests.
>
> Thus this is the recommended way of doing.

### Routing
Any route that is not declared in eZ Platform in an included `routing.yml` and that is not a valid *UrlAlias* will automatically fallback
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
