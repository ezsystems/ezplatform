<?php
/**
 * Adds scripts to post-install-cmd and post-update-cmd composer.json blocks
 */
$scripts = [
    "eZ\\Bundle\\EzPublishLegacyBundle\\Composer\\ScriptHandler::installAssets",
    "eZ\\Bundle\\EzPublishLegacyBundle\\Composer\\ScriptHandler::installLegacyBundlesExtensions"
];

$composer = json_decode( file_get_contents( 'composer.json' ), true );
foreach ( $scripts as $script )
{
    $composer['scripts']['post-update-cmd'][] = $script;
    $composer['scripts']['post-install-cmd'][] = $script;
}

file_put_contents( 'composer.json', json_encode( $composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) . "\n" );
