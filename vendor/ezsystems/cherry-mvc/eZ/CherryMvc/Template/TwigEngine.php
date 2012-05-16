<?php
/**
 * File containing the TwigEngine class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\CherryMvc\Template;

use \Twig_Environment;
use \Twig_Loader_String;

class TwigEngine implements Engine
{
    /**
     * @var \Twig_Environment
     */
    private $twigEnv;

    public function build()
    {
        $this->twigEnv = new Twig_Environment( new Twig_Loader_String() );
    }

    public function render( $template, array $params = array() )
    {
        return $this->twigEnv->render( $template, $params );
    }
}
