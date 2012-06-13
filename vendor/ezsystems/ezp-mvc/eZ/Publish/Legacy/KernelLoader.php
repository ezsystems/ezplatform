<?php
/**
 * File containing the KernelLoader class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Publish\Legacy;

use eZ\Publish\Legacy\Kernel as LegacyKernel;

/**
 * Legacy kernel loader
 */
class KernelLoader
{
    /**
     * Builds up the legacy kernel and encapsulates it inside a closure, allowing lazy loading.
     *
     * @param string $legacyRootDir Absolute path to the legacy root directory (eZPublish 4 install dir)
     * @param string $webrootDir Absolute path to the new webroot directory (web/)
     * @return \Closure
     */
    public function buildLegacyKernel( $legacyRootDir, $webrootDir )
    {
        return function() use ( $legacyRootDir, $webrootDir )
        {
            static $legacyKernel;
            if ( !$legacyKernel instanceof LegacyKernel )
                $legacyKernel = new LegacyKernel( $legacyRootDir, $webrootDir );

            return $legacyKernel;
        };
    }
}
