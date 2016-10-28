eZ Platform Varnish configuration
=================================

Prerequisites
-------------
* A working Varnish 4.1 and higher setup with xkey module installed.

Recommended VCL base files
--------------------------
For Varnish to work properly with eZ, you'll need to use one of the provided files as a basis:

* [eZ Platform optimized Varnish VCL using xkey](https://github.com/ezsystems/ezplatform-http-cache/blob/master/docs/varnish/vcl/varnish4.vcl)
  * Further documentation on this option can be found in [ezplatform-http-cache package](https://github.com/ezsystems/ezplatform-http-cache/tree/master/docs)
* [(Deprecated) eZ Platform optimized Varnish VCL using BAN](vcl/varnish_ban.vcl)
  * To use this one you'll need to disable `EzSystemsPlatformHttpCacheBundle`.

> **Note:** Http cache management is done with the help of [FOSHttpCacheBundle](http://foshttpcachebundle.readthedocs.org/).
  One may need to tweak their VCL further on according to [FOSHttpCache documentation](http://foshttpcache.readthedocs.org/en/latest/varnish-configuration.html)
  in order to use features supported by it.
