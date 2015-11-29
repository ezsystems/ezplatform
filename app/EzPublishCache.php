<?php
/**
 * File containing the EzPublishCache class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */
use eZ\Bundle\EzPublishCoreBundle\HttpCache;

/**
 * Class EzPublishCache.
 *
 * For easier upgrade do not change this file, as of 2015.01 possible to extend
 * cleanly via HTTP_CACHE_CLASS & CUSTOM_CLASSLOADER_FILE env variables!
 */
class EzPublishCache extends HttpCache
{
}
