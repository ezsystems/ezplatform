<?php
/**
 * File containing the AddFieldTypePass class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * This compiler pass will register eZ Publish field types.
 */
class AddFieldTypePass implements CompilerPassInterface
{

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process( ContainerBuilder $container )
    {
        if ( !$container->hasDefinition( 'ezpublish.api.repository.factory' ) )
            return;

        $repositoryFactoryDef = $container->getDefinition( 'ezpublish.api.repository.factory' );

        foreach ( $container->findTaggedServiceIds( 'ezpublish.fieldType' ) as $id => $attributes )
        {
            $repositoryFactoryDef->addMethodCall(
                'registerFieldType',
                array(
                     // Only pass the service Id since field types will be lazy loaded via the service container
                     $id,
                     // TODO: Maybe there should be some validation here. What if no alias is provided ?
                     $attributes[0]['alias']
                )
            );
        }
    }
}
