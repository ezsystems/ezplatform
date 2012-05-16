<?php
/**
 * File containing the template Engine interface.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\CherryMvc\Template;

interface Engine
{
    /**
     * Builds the template engine
     *
     * @return \eZ\CherryMvc\Template\Engine
     */
    public function build();

    /**
     * Renders a template.
     * $params should be injected into $template
     *
     * @param string $template Path to template or directly template content.
     * @param array $params Parameters to inject. Key is variable name.
     * @return string
     */
    public function render( $template, array $params = array() );
}
