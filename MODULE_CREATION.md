Creating a module
=================

Documentation
-------------

eZ Publish 5 relying on Symfony2 and twig, modules are created by using
[Bundles](http://symfony.com/doc/current/bundles/).

The best way to learn on how to interact within the new Symfony stack is to
read the [available documentation](http://symfony.com/doc/current/book/page_creation.html).

Special note
------------

Autoloading
~~~~~~~~~~~

Custom autoloaders may be plugged-in. Create the file `ezpublish/config/autoload.php`
which will be automatically included if it exists. An example of how this file
can be used is shown in `ezpublish/config/autoload.php-EXAMPLE`.

Please DO NOT modify `ezpublish/autoload.php`.
