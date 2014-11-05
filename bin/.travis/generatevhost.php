#!/usr/bin/php
<?php
/**
 * PHP template file for apache vhost config
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 *
 * @internal
 * @todo Exposed in symfony command and take advantage of components for better input validation if this ever
 *       is supposed to be moved out of private folder.
 */

$generator = new ezpBinTravisHttpConfGenerator(
    "h::",
    array( 'basedir:', 'ip_address::', 'port::', 'host::', 'host_alias::', 'env::', 'proxy::', 'help', 'dry-run' ),
    array(
        'basedir' => null,// required
        'ip_address' => '*',
        'port' => 80,
        'host' => 'localhost',
        'host_alias' => '*.localhost',
        'env' => 'prod',
        'proxy' => '127.0.0.1'
    )
);

// Display help if requested
$options = $generator->getOptions();
if ( isset( $options['h'] ) || isset( $options['help'] ) )
{
    $generator->helpText();
    exit( 0 );
}

// Validate provided template file name
$templateFile = $argc > 1 ? $argv[$argc - 2] : null;
if ( empty( $templateFile ) )
{
    $generator->helpText( "Error: Path to template_file missing" );
    exit( 1 );
}
else if ( !file_exists( $templateFile ) )
{
    $generator->helpText( "Error: Could not find template_file '{$templateFile}'" );
    exit( 1 );
}

// Validate provided template file name
$outputFile = $argv[$argc - 1];
if ( empty( $outputFile ) )
{
    $generator->helpText( "Error: Path to output_file missing" );
    exit( 1 );
}
else if ( !($fh = fopen( $outputFile, 'w' )) )
{
    $generator->helpText( "Error: Can not write to output_file '{$outputFile}'" );
    exit( 1 );
}

// Validate basedir
if ( empty( $options['basedir'] ) )
{
    $generator->helpText( "Basedir argument missing." );
    exit( 1 );
}
else if ( !is_dir( $options['basedir'] ) )
{
    $generator->helpText( "Could not find basedir '{$options['basedir']}'." );
    exit( 1 );
}

// Generate keys and values for replacement
$keys = $values = array();
foreach ( $options as $key => $value )
{
    $keys[] = "%" . strtoupper( $key ) . "%";
    $values[] = $value;
}

// Generate result and either echo or attempt to write to disk
$result = str_replace( $keys, $values, file_get_contents( $templateFile ) );
if ( isset( $options['dry-run'] ) )
{
    echo $result;
    exit( 0 );
}

if ( fwrite( $fh, $result ) === false )
{
    echo "\n\033[0;31mVHost configuration failed to generated to '{$outputFile}' for unknown reason, user rights?\033[0m\n";
    exit( 1 );
}

echo "\n\033[0;35mVHost configuration successfully generated to '{$outputFile}'\033[0m\n";
fclose( $fh );
exit( 0 );

/**
 * Class ezpBinTravisHttpConfGenerator
 * @internal
 */
class ezpBinTravisHttpConfGenerator
{
    /**
     * @var string
     */
    private $options;

    /**
     * @var array
     */
    private $longopts;

    /**
     * @var array
     */
    private $defaults;

    /**
     * @param string $options
     * @param array $longopts
     * @param array $defaults
     */
    public function __construct( $options, array $longopts, array $defaults )
    {
        $this->options = $options;
        $this->longopts = $longopts;
        $this->defaults = $defaults;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return getopt( $this->options, $this->longopts ) + $this->defaults;
    }

    /**
     * @param string $error
     */
    public function helpText( $error = '' )
    {
        $errorText = $error ? "\n\033[0;31m$error\033[0m\n" : '';
        echo <<<EOD
$errorText
\033[0;35mAbout: Script to generate http configuration for apache or nginx.\033[0m

Format:
  \$ php generate_vhost.php
    --basedir=/var/www
    [--host=localhost]
    [--ip_address=*]
    [--port=80]
    [--env=dev]
    [--proxy=127.0.0.1] : Used for http cache if you selected prod template
    [-h|--help]         : Help text, this one more or less
    [--dry-run]         : Generate the result but echo to screen instead of writing
    template_file       : The file to use as template for the generated ouput file
    output_file         : The output file, usually vhost for apache2 or nginx

Example use:
  \$ sudo php bin/.travis/generate_vhost.php --basedir=/var/www --env=dev doc/apache2/vhost.template /etc/apache2/sites-available/my.com

  Note: In the Apache example above you would need to enable the site before you can use it.

EOD;
        echo "\nAvailable options:\n";
        $options = $this->defaults +
            array_fill_keys(
                array_map(
                    function( $v )
                    {
                        return str_replace( ':', '', $v );
                    },
                    $this->longopts
                ),
                ""
            ) +
            array_fill_keys( str_split( str_replace( ':', '', $this->options ) ), "" );
        echo "  " . implode( ", ", array_keys( $options ) ) . "\n\n";
    }
}
