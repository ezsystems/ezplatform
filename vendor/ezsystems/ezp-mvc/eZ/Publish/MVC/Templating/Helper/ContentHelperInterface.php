<?php
/**
 * File containing the ContentHelperInterface interface.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC\Templating\Helper;

use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;

/**
 * Interface for an eZ Publish content helper.
 * A content helper exposes shorthand methods to a template engine for content display.
 */
interface ContentHelperInterface
{
    /**
     * Renders the HTML for a given content.
     *
     * @abstract
     * @param \eZ\Publish\Core\Repository\Values\Content\Content $content
     * @param array $params An array of parameters to the content view
     * @return string The HTML markup
     */
    public function renderContent( Content $content, array $params = array() );

    /**
     * Renders the HTML for a given field.
     *
     * @abstract
     * @param \eZ\Publish\Core\Repository\Values\Content\Content $content
     * @param string $fieldIdentifier
     * @param array $params An array of parameters to pass to the field view
     * @throws \InvalidArgumentException If $fieldIdentifier is invalid in $content
     * @return string The HTML markup
     */
    public function renderField( Content $content, $fieldIdentifier, array $params = array() );
}
