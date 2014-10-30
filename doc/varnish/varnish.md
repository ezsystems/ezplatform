# eZ Publish Varnish configuration

## Prerequisites
* A working Varnish 3 or Varnish 4 setup.

## Recommended VCL base files
For Varnish to work properly with eZ, you'll need to use one of the provided files as a basis:

* [eZ 5.4+ / 2014.09+ with Varnish 3](vcl/varnish3.vcl)
* [eZ 5.4+ / 2014.09+ with Varnish 4](vcl/varnish4.vcl)

> **Note:** Http cache management is done with the help of [FOSHttpCacheBundle](http://foshttpcachebundle.readthedocs.org/).
  One may need to tweak their VCL further on according to [FOSHttpCache documentation](http://foshttpcache.readthedocs.org/en/latest/varnish-configuration.html)
  in order to use features supported by it.
