<?php
/**
 * File containing the ViewProvider class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\DemoBundle\Content;

use eZ\Publish\MVC\View\ContentViewProvider,
    eZ\Publish\API\Repository\Values\Content\Location,
    eZ\Publish\API\Repository\Values\Content\ContentInfo,
    eZ\Publish\MVC\View\ContentView;

class ViewProvider implements ContentViewProvider
{
    /**
     * Returns a ContentView object corresponding to $contentInfo, or void if not applicable
     *
     * @param \eZ\Publish\API\Repository\Values\Content\ContentInfo $contentInfo
     * @param string $viewType
     * @return \eZ\Publish\MVC\View\ContentView|void
     */
    public function getViewForContent( ContentInfo $contentInfo, $viewType )
    {
        $contentView = null;
        switch ( $contentInfo->contentType->identifier )
        {
            case 'small_folder':
                $contentView = new ContentView( "eZDemoBundle:$viewType:small_folder.html.twig" );
                break;
        }

        return $contentView;
    }

    /**
     * Returns a ContentView object corresponding to $location, or void if not applicable
     *
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param string $viewType
     * @return \eZ\Publish\MVC\View\ContentView|void
     */
    public function getViewForLocation( Location $location, $viewType )
    {
        return $this->getViewForContent( $location->getContentInfo(), $viewType );
    }
}
