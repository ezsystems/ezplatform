<?php
/**
 * File containing the LegacyKernelController class.
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishLegacyBundle\Controller;

use eZ\Publish\MVC\Controller;
use Symfony\Component\HttpFoundation\Response;
use \ezpKernel;

/**
 * Controller embedding legacy kernel.
 */
class LegacyKernelController extends Controller
{
    public function indexAction()
    {
        $kernel = $this->container->get( 'ezpublish_legacy.kernel' );
        $result = $kernel->run();

        return new Response(
            $result["content"]
        );
    }
}
