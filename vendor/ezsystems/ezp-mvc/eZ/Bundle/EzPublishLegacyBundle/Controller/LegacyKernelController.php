<?php
/**
 * File containing the LegacyKernelController class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishLegacyBundle\Controller;

use Symfony\Component\HttpFoundation\Response;
use eZ\Bundle\EzPublishLegacyBundle\Services\LegacyKernel;

/**
 * Controller embedding legacy kernel.
 */
class LegacyKernelController
{
    /**
     * The legacy kernel instance (eZ Publish 4)
     *
     * @var \eZ\Bundle\EzPublishLegacyBundle\Services\LegacyKernel
     */
    private $kernel;

    public function __construct( LegacyKernel $kernel )
    {
        $this->kernel = $kernel;
    }

    /**
     * Base fallback action.
     * Will be basically used for every
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $result = $this->kernel->run();

        return new Response(
            $result["content"]
        );
    }
}
