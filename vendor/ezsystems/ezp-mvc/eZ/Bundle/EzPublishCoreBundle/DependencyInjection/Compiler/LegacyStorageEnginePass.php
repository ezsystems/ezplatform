<?php
/**
 * File containing the LegacyStorageEnginePass class.
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
class LegacyStorageEnginePass implements CompilerPassInterface
{

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process( ContainerBuilder $container )
    {
        if ( !$container->hasDefinition( 'ezpublish.api.storage_engine.legacy.factory' ) )
            return;

        $legacyStorageEngineDef = $container->getDefinition( 'ezpublish.api.storage_engine.legacy.factory' );

        foreach ( $container->findTaggedServiceIds( 'ezpublish.storageEngine.legacy.converter' ) as $id => $attributes )
        {
            $legacyStorageEngineDef->addMethodCall(
                'registerFieldTypeConverter',
                array(
                     // TODO: Maybe there should be some validation here. What if no alias is provided ?
                     $attributes[0]['alias'],
                     $container->getDefinition( $id )->getClass()
                )
            );
        }

        foreach ( $container->findTaggedServiceIds( 'ezpublish.storageEngine.legacy.externalStorageHandler' ) as $id => $attributes )
        {
            $legacyStorageEngineDef->addMethodCall(
                'registerFieldTypeExternalStorageHandler',
                array(
                     // TODO: Maybe there should be some validation here. What if no alias is provided ?
                     $attributes[0]['alias'],
                     $container->getDefinition( $id )->getClass()
                )
            );
        }
    }
}
