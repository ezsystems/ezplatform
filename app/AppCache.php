<?php

use EzSystems\PlatformHttpCacheBundle\AppCache as PlatformHttpCacheBundleAppCache;

/**
 * Class AppCache.
 *
 * For easier upgrade do not change this file, as of 2015.01 possible to extend
 * cleanly via SYMFONY_HTTP_CACHE_CLASS & SYMFONY_CLASSLOADER_FILE env variables!
 */
class AppCache extends PlatformHttpCacheBundleAppCache
{
}
