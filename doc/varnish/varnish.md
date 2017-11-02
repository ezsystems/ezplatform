eZ Platform Varnish configuration
=================================

Prerequisites
-------------
* A working Varnish 3 or Varnish 4 setup.

Recommended VCL base files
--------------------------
For Varnish to work properly with eZ, you'll need to use the following VCL as starting point:

* [eZ Platform 1.7+ with Varnish 4.x/5.x with xkey VMOD](vcl/varnish4_xkey.vcl)
* [eZ 5.4+ / 2014.09+ with Varnish 4/5 using builtin BAN](vcl/varnish4_ban.vcl) _(Deprecated in favour of xkey version)_
* [eZ 5.4+ / 2014.09+ with Varnish 3 using builtin BAN](vcl/varnish3_ban.vcl) _(Deprecated in favour of xkey version)_


> **Note:** Http cache management is done with the help of [FOSHttpCacheBundle](http://foshttpcachebundle.readthedocs.org/).
  One may need to tweak their VCL further on according to [FOSHttpCache documentation](http://foshttpcache.readthedocs.org/en/latest/varnish-configuration.html)
  in order to use features supported by it.
