<?php
/**
 * File containing the ContentHelper class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC\Templating\Twig\Helper;

use eZ\Publish\MVC\Templating\Helper\ContentHelperInterface;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;

class ContentHelper implements ContentHelperInterface
{
    /**
     * Renders the HTML for a given content.
     *
     * @param \eZ\Publish\Core\Repository\Values\Content $content
     * @param array $params An array of parameters to the content view
     * @return string The HTML markup
     */
    public function renderContent( Content $content, array $params = array() )
    {
        $viewType = isset( $params['viewType'] ) ? $params['viewType'] : 'full';
        return $content->contentInfo->name;
    }

    /**
     * Renders the HTML for a given field.
     *
     * @param \eZ\Publish\Core\Repository\Values\Content\Content $content
     * @param string $fieldIdentifier Identifier for the field we want to render
     * @param array $params An array of parameters to pass to the field view
     * @throws \InvalidArgumentException If $fieldIdentifier is invalid in $content
     * @return string The HTML markup
     */
    public function renderField( Content $content, $fieldIdentifier, array $params = array() )
    {
        $lang = isset( $params['lang'] ) ? $params['lang'] : null;
        $field = $content->getField( $fieldIdentifier, $lang );
        if ( !$field instanceof Field )
            throw new \InvalidArgumentException( "Invalid field identifier '$fieldIdentifier' for content #{$content->contentInfo->id}" );

        return $field->value;
    }
}
