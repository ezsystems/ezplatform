<?php
/**
 * File containing the UrlMatcherEvent class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Routing\RequestContext;

abstract class UrlMatcherEvent extends Event
{
    /**
     * Context of the request the kernel is currently processing
     *
     * @var \Symfony\Component\Routing\RequestContext
     */
    private $context;

    /**
     * @param \Symfony\Component\Routing\RequestContext $context
     */
    public function __construct( RequestContext $context )
    {
        $this->context = $context;
    }

    /**
     * Returns the current request context
     *
     * @return \Symfony\Component\Routing\RequestContext
     */
    public function getContext()
    {
        return $this->context;
    }
}
