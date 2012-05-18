<?php
/**
 * File containing the LegacyKernel class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishLegacyBundle\Services;
use \ezpKernel;

class LegacyKernel extends ezpKernel
{
    /**
     * Legacy root directory
     *
     * @var string
     */
    private $legacyRootDir;

    /**
     * Original webroot directory
     *
     * @var string
     */
    private $webrootDir;

    private $isRequestInitialized = false;

    public function __construct( $legacyRootDir, $webrootDir )
    {
        $this->legacyRootDir = $legacyRootDir;
        $this->webrootDir = $webrootDir;

        $this->enterLegacyRootDir();
        parent::__construct();
    }

    /**
     * Changes the current working directory to the legacy root dir.
     * Calling this method is mandatory to use legacy kernel since a lot of resources in eZ Publish 4.x relatively defined.
     */
    public function enterLegacyRootDir()
    {
        if ( getcwd() != $this->legacyRootDir )
            chdir( $this->legacyRootDir );
    }

    /**
     * Leaves the legacy root dir and switches back to the initial webroot dir.
     */
    public function leaveLegacyRootDir()
    {
        if ( getcwd() == $this->legacyRootDir )
            chdir( $this->webrootDir );
    }

    /**
     * Initializes request in legacy kernel.
     * This will happen only once.
     */
    protected function requestInit()
    {
        if ( $this->isRequestInitialized )
            return;

        parent::requestInit();
        $this->isRequestInitialized = true;
    }

    /**
     * Runs current request through legacy kernel.
     *
     * @return array
     */
    public function run()
    {
        $this->enterLegacyRootDir();
        return parent::run();
    }

    public function shutdown()
    {
        parent::shutdown();
        $this->leaveLegacyRootDir();
    }
}
