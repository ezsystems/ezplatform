<?php
/**
 * File containing the LegacyIncludeNode class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Legacy\Templating\Twig\Node;

/**
 * Represents an ez_legacy_include node
 */
class LegacyIncludeNode extends \Twig_Node
{
    public function __construct( \Twig_Node_Expression $tplPath, \Twig_Node_Expression $params, $lineno, $tag = null )
    {
        return parent::__construct(
            array(
                 'tplPath'      => $tplPath,
                 'params'       => $params
            ),
            array(),
            $lineno,
            $tag
        );
    }

    public function compile( \Twig_Compiler $compiler )
    {
        $compiler
            ->addDebugInfo( $this )
            ->write( "echo \$this->env->getExtension('ezpublish.legacy')->renderTemplate(" )
            ->subcompile( $this->getNode( 'tplPath' ) )
            ->raw( ', ' )
            ->subcompile( $this->getNode( 'params' ) )
            ->raw( ");\n" )
        ;
    }
}
