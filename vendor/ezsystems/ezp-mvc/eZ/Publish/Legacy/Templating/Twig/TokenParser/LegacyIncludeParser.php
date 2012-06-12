<?php
/**
 * File containing the LegacyIncludeParser class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Legacy\Templating\Twig\TokenParser;
use eZ\Publish\Legacy\Templating\Twig\Node\LegacyIncludeNode;

class LegacyIncludeParser extends \Twig_TokenParser
{

    /**
     * Parses a token and returns a node.
     *
     * @param \Twig_Token $token A Twig_Token instance
     *
     * @return \Twig_NodeInterface A Twig_NodeInterface instance
     */
    public function parse( \Twig_Token $token)
    {
        $exprParser = $this->parser->getExpressionParser();
        $stream = $this->parser->getStream();

        // Template path is the first token to be parsed
        $tplPath = $exprParser->parseExpression();

        // Parameters
        if ( $stream->test( \Twig_Token::NAME_TYPE, 'with' ) )
        {
            $stream->next();
            $params = $exprParser->parseExpression();
        }
        else
        {
            $params = new \Twig_Node_Expression_Array( array(), $token->getLine() );
        }

        $stream->expect( \Twig_Token::BLOCK_END_TYPE );

        return new LegacyIncludeNode( $tplPath, $params, $token->getLine(), $this->getTag() );
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'ez_legacy_include';
    }
}
