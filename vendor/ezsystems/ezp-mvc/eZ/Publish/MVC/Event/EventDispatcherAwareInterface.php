<?php
/**
 * File containing the EventDispatcherAwareInterface interface.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface EventDispatcherAwareInterface
{
    /**
     * Gets the dispatcher
     *
     * @abstract
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getDispatcher();

    /**
     * Sets the dispatcher
     *
     * @abstract
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     * @return void
     */
    public function setDispatcher( EventDispatcherInterface $dispatcher );
}
