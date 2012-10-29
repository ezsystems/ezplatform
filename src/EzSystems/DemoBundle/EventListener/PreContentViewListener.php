<?php
/**
 * File containing the PreContentViewListener class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\DemoBundle\EventListener;

use eZ\Publish\Core\MVC\Symfony\Event\PreContentViewEvent;

class PreContentViewListener
{
    public function onPreContentView( PreContentViewEvent $event )
    {
        $contentView = $event->getContentView();
        $contentView->addParameters(
            array(
                'foo' => 'bar',
                'osTypes' => array( 'osx', 'linux', 'win' )
            )
        );
    }
}
