<?php
require_once __DIR__ . '/../app/autoload.php';
require_once __DIR__ . '/../app/EzPublishKernel.php';
require_once __DIR__ . '/../app/EzPublishCache.php';

use eZ\Publish\MVC\SiteAccess\Router as SiteAccessRouter;

$kernel = new EzPublishKernel( 'dev', true );
$kernelCache = new EzPublishCache( $kernel );
$kernelCache->handle(
    $kernel->createRequestFromGlobals(
        new SiteAccessRouter(
            "ezdemo_site",
            array(
                "uri" => array(
                    "ezdemo_site" => "ezdemo_site",
                    "ezdemo_site_admin" => "ezdemo_site_admin",
                ),
                "host" => array(
                    "ezpublish" => "ezdemo_site",
                    "ezpublish.admin" => "ezdemo_site_admin",
                ),
            )
        )
    )
)->send();
