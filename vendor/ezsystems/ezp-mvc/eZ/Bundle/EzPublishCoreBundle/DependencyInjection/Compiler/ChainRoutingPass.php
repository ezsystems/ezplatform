<?php
/**
 * File containing the ChainRoutingPass class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * The ChainRoutingPass will register all services tagged as "router" to the chain router.
 */
class ChainRoutingPass implements CompilerPassInterface
{
    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process( ContainerBuilder $container )
    {
        if ( !$container->hasDefinition( 'ezpublish.chain_router' ) )
            return;

        $chainRouter = $container->getDefinition( 'ezpublish.chain_router' );

        // Enforce default router to be part of the routing chain
        // The default router will be given the highest priority so that it will be used by default
        if ( $container->hasDefinition( 'router.default' ) )
        {
            $defaultRouter = $container->getDefinition( 'router.default' );
            if ( !$defaultRouter->hasTag( 'router' ) )
            {
                $defaultRouter->addTag(
                    'router',
                    array( 'priority' => 255 )
                );
            }
        }

        foreach ( $container->findTaggedServiceIds( 'router' ) as $id => $attributes )
        {
            $priority = isset( $attributes[0]['priority'] ) ? (int)$attributes[0]['priority'] : 0;
            // Priority range is between -255 (the lowest) and 255 (the highest)
            if ( $priority > 255 )
                $priority = 255;
            if ( $priority < -255 )
                $priority = -255;

            $chainRouter->addMethodCall(
                'addRouter',
                array(
                     new Reference( $id ),
                     $priority
                )
            );
        }
    }
}
