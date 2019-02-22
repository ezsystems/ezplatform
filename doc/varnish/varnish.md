eZ Platform Varnish configuration
=================================

Prerequisites
-------------
* A working Varnish 5.1 or higher _(6.0 is a LTS, so the recommended version we test against)_.
* [Varnish xkey module](https://github.com/varnish/varnish-modules/)

Recommended VCL base files
--------------------------
Provided VCL for eZ can be found in [vendor/ezsystems/ezplatform-http-cache/docs/varnish](https://github.com/ezsystems/ezplatform-http-cache/tree/0.8/docs/varnish). Specifically `/vcl/varnish5.vcl`.


> **Note:** Http cache management is done with the help of [FOSHttpCacheBundle](http://foshttpcachebundle.readthedocs.org/).
  One may need to tweak their VCL further on according to [FOSHttpCache documentation](http://foshttpcache.readthedocs.org/en/latest/varnish-configuration.html)
  in order to use features supported by it.
