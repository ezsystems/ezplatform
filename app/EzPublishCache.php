<?php
/**
 * File containing the EzPublishCache class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

class EzPublishCache extends HttpCache
{
    protected function getOptions()
    {
        return array(
            "debug" => false,
            "default_ttl" => 0,
            "private_headers" => array( "Authorization", "Cookie" ),
            "allow_reload" => false,
            "allow_revalidate" => false,
            "stale_while_revalidate" => 2,
            "stale_if_error" => 60,
        );
    }
}
