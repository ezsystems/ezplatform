<?php
/**
 * File containing the wrapper around the legacy index_treemenu.php file
 *
 * @copyright Copyright (C) 1999-2012 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

$legacyRoot = __DIR__ . "/../app/ezpublish_legacy/";
chdir( $legacyRoot );
require $legacyRoot . "index_treemenu.php";
