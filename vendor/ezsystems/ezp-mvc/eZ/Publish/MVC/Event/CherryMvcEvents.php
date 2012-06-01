<?php
/**
 * File containing the CherryMvcEvents class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC\Event;

/**
 * Contains all events thrown in CherryMvc
 */
final class CherryMvcEvents
{
    /**
     * The URL_ALIAS_MATCH event occurs when trying to match current URI to an internal URL Alias.
     * Gives a chance to provide an Url Alias matcher.
     */
    const URL_ALIAS_MATCH = 'ezpublish.url_alias';

    /**
     * POST_ROUTE is triggered after the URI matching loop, if no URI has been matched.
     * It gives a chance to provide a fallback (useful for BC with legacy eZ Publish 4)
     */
    const FALLBACK = 'ezpublish.fallback';
}
