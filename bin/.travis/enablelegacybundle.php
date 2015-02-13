<?php
/**
 * This file is part of the eZ Publish Kernel package
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

$kernelPath = 'ezpublish/EzPublishKernel.php';

$kernelContents = file_get_contents( $kernelPath );
$kernelContents = preg_replace(
    "/$\s+\);$$\s+switch/m",
    ",\n            new eZ\Bundle\EzPublishLegacyBundle\EzPublishLegacyBundle( \$this )\n        );\n\n        switch",
    $kernelContents
);

file_put_contents( $kernelPath, $kernelContents );
