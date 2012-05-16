<?php
/**
 * File containing the Template Factory class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\CherryMvc\Template;

use \InvalidArgumentException;

class Factory
{
    protected $templateEngines = array();

    /**
     * Register a template kind with its associated callback function
     *
     * @param string $name
     * @param \eZ\CherryMvc\Template\Engine $engine
     */
    public function register( $name, Engine $engine )
    {
        $this->templateEngines[$name] = $engine;
        $this->templateEngines[$name]->build();
    }

    /**
     * Factory for template engines
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     * @return mixed
     */
    public function getEngine( $name )
    {
        if ( !isset( $this->templateEngines[$name] ) )
        {
            throw new \InvalidArgumentException( "Template engine '$name' has not been registered!" );
        }

        return $this->templateEngines[$name];
    }
}
