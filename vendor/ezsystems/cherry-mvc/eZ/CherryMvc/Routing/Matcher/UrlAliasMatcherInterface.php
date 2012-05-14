<?php
/**
 * File containing the UrlAliasMatcherInterface class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\CherryMvc\Routing\Matcher;

interface UrlAliasMatcherInterface
{
    /**
     * Matches $request to content with URL alias system.
     * Returns a hash of parameters including :
     *  - _controller (controller to call, must be callable)
     *  - _route (name of the dynamically matched route)
     *  - All parameters to pass to the controller (param name is the key)
     *
     * Example :
     * array(
     *     '_route'                  => 'eZUrlAlias',
     *     '_controller'             => 'My\\Controller::contentViewAction',
     *     'contentId'               => 2,
     *     'version'                 => 3
     * )
     *
     * Returns null if no UrlAlias can be matched against $pathinfo
     *
     * @abstract
     * @param string $pathinfo Path info of the current request. (e.g. "/my/content")
     * @return array|null
     */
    public function match( $pathinfo );
}
