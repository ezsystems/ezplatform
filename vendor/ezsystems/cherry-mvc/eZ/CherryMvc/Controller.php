<?php
/**
 * File containing the Controller class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\CherryMvc;

use eZ\CherryMvc\Template\Factory as TemplateFactory;

class Controller
{
    /**
     * Template engine factory
     *
     * @var \eZ\CherryMvc\Template\Factory
     */
    protected $templateEngineFactory;

    /**
     * Sets the template factory to use
     *
     * @param \eZ\CherryMvc\Template\Factory $templateEngineFactory
     *
     */
    public function setTemplateEngineFactory( TemplateFactory $templateEngineFactory )
    {
        $this->templateEngineFactory = $templateEngineFactory;
    }
}
