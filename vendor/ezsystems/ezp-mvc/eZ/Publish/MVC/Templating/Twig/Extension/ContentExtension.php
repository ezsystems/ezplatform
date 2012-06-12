<?php
/**
 * File containing the ContentExtension class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\MVC\Templating\Twig\Extension;

use \Twig_Extension;
use \Twig_Environment;
use \Twig_Function_Method;
use \Twig_Template;
use eZ\Publish\Core\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;

/**
 * Twig content extension for eZ Publish specific usage.
 * Exposes helpers to play with public API objects.
 */
class ContentExtension extends Twig_Extension
{
    /**
     * Array of Twig template resources.
     * Either path to each template is referenced or its \Twig_Template (compiled) counterpart
     *
     * @var string[]|\Twig_Template[]
     */
    protected $resources;

    /**
     * A \Twig_Template instance used to render template blocks.
     *
     * @var \Twig_Template
     */
    protected $template;

    /**
     * The Twig environment
     *
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * Template blocks, by field
     *
     * @var \SplObjectStorage
     */
    protected $blocks;

    public function __construct( array $resources = array() )
    {
        $this->resources = $resources;
        $this->blocks = new \SplObjectStorage();
    }

    /**
     * Initializes the template runtime (aka Twig environment).
     *
     * @param \Twig_Environment $environment
     */
    public function initRuntime( Twig_Environment $environment )
    {
        $this->environment = $environment;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array
     */
    public function getFunctions()
    {
        return array(
            'ez_render_field' => new Twig_Function_Method(
                $this,
                'renderField',
                array( 'is_safe' => array( 'html' ) )
            )
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'ezpublish.content';
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
        // Merging passed parameters to default ones
        $params += array(
            'lang'      => null,
        );

        $field = $content->getField( $fieldIdentifier, $params['lang'] );
        if ( !$field instanceof Field )
            throw new \InvalidArgumentException( "Invalid field identifier '$fieldIdentifier' for content #{$content->contentInfo->id}" );

        // Getting instance of Twig_Template that will be used to render blocks
        $params['field'] = $field;
        $this->template = reset( $this->resources );
        if ( !$this->template instanceof Twig_Template )
            $this->template = $this->environment->loadTemplate( $this->template );

        $html = $this->template->renderBlock(
            $this->getFieldBlockName( $content, $field ),
            $params,
            $this->getBlocksByField( $content, $field )
        );

        return $html;
    }

    /**
     * Returns template blocks for $field.
     * Template block convention name is <fieldTypeIdentifier>_field
     * Example: 'ezstring_field' will be relevant for a full view of ezstring field type
     *
     * @param Content $content
     * @param Field $field
     * @return array
     * @throws \LogicException If no template block can be found for $field
     */
    protected function getBlocksByField( Content $content, Field $field )
    {
        if ( !$this->blocks->contains( $field ) )
        {
            // Looping against available resources to find template blocks for $field
            //TODO: maybe we should consider "themes" like in forms - http://symfony.com/doc/master/book/forms.html#form-theming
            $blocks = array();
            foreach ( $this->resources as &$template )
            {
                if ( !$template instanceof Twig_Template )
                    $template = $this->environment->loadTemplate( $template );

                $tpl = $template;
                $fieldBlockName = $this->getFieldBlockName( $content, $field );

                // Current template might have parents, so we need to loop against them to find a matching block
                do
                {
                    foreach ( $tpl->getBlocks() as $blockName => $block )
                    {
                        if ( strpos( $blockName, $fieldBlockName ) === 0 )
                        {
                            $blocks[$blockName] = $block;
                        }
                    }
                }
                while ( $tpl = $tpl->getParent( array() ) !== false );
            }

            if ( empty( $blocks ) )
                throw new \LogicException( "Cannot find '$fieldBlockName' template block field type." );

            $this->blocks->attach( $field, $blocks );
        }
        else
        {
            $blocks = $this->blocks[$field];
        }

        return $blocks;
    }

    /**
     * Returns expected block name for $field, attached in $content.
     *
     * @param \eZ\Publish\Core\Repository\Values\Content\Content $content
     * @param \eZ\Publish\API\Repository\Values\Content\Field $field
     * @return string
     */
    protected function getFieldBlockName( Content $content, Field $field )
    {
        $fieldTypeIdentifier = $content
            ->getVersionInfo()
            ->getContentInfo()
            ->getContentType()
            ->getFieldDefinition( $field->fieldDefIdentifier )
            ->fieldTypeIdentifier;
        return "{$fieldTypeIdentifier}_field";
    }
}
