<?php
/**
 * File containing the FeatureContext class.
 *
 * This class contains general feature context for Behat.
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Features\Context;

use Behat\Behat\Event\OutlineExampleEvent;
use Behat\Behat\Event\ScenarioEvent;
use Behat\MinkExtension\Context\MinkContext;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use eZ\Publish\Core\MVC\Symfony\SiteAccess;
use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Gherkin\Node\TableNode;

/**
 * Feature context.
 */
class FeatureContext extends MinkContext implements KernelAwareInterface
{
    const DEFAULT_SITEACCESS_NAME = 'behat_site';

    /**
     * @var \Symfony\Component\HttpKernel\KernelInterface
     */
    protected $kernel;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var array Array to map identifier to urls, should be set by child classes.
     *
     * Important:
     *  this is an associative array( ex: array( 'key' => '/some/url') ) they keys
     *  should be set (on contexts) as lower cases since the
     *  FeatureContext::getPathByPageIdentifier() will check for lower case
     */
    protected $pageIdentifierMap = array();

    /**
     * This will contains the source path for media files
     *
     * ex:
     * $fileSource = array(
     * 	    "Video 1" => "/var/storage/original/media/video1.mp4",
     * );
     *
     * @var array This will have a ( 'identifier' => 'path' )
     */
    protected $fileSource = array();

    /**
     * Initializes context with parameters from behat.yml.
     *
     * @param array $parameters
     */
    public function __construct( array $parameters )
    {
        $this->parameters = $parameters;
    }

    /**
     * Sets HttpKernel instance.
     * This method will be automatically called by Symfony2Extension ContextInitializer.
     *
     * @param KernelInterface $kernel
     */
    public function setKernel( KernelInterface $kernel )
    {
        $this->kernel = $kernel;
    }

    /**
     * Get repository
     *
     * @return \eZ\Publish\API\Repository\Repository
     */
    public function getRepository()
    {
        return $this->kernel->getContainer()->get( 'ezpublish.api.repository' );
    }

    /**
     * @BeforeScenario
     *
     * @param ScenarioEvent|OutlineExampleEvent $event
     */
    public function prepareFeature( $event )
    {
        // Inject a properly generated siteaccess if the kernel is booted, and thus container is available.
        $this->kernel->getContainer()->set( 'ezpublish.siteaccess', $this->generateSiteAccess() );
    }

    /**
     * Generates the siteaccess
     *
     * @return \eZ\Publish\Core\MVC\Symfony\SiteAccess
     */
    protected function generateSiteAccess()
    {
        $siteAccessName = getenv( 'EZPUBLISH_SITEACCESS' );
        if ( !$siteAccessName )
        {
            $siteAccessName = static::DEFAULT_SITEACCESS_NAME;
        }

        return new SiteAccess( $siteAccessName, 'cli' );
    }

    /**
     * This function will convert Gherkin tables into structure array of data
     *
     * if Gherkin table look like
     *
     *      | field  | value1         | value2 | ... | valueN |
     *      | field1 | single value 1 |        | ... |        |
     *      | field2 | single value 2 |        | ... |        |
     *      | field3 | multiple       | value  | ... | here   |
     *
     * the returned array should look like:
     *      $data = array(
     *          "field1" => "single value 1",
     *          "field2" => "single value 2",
     *          "field3" => array( "multiple", "value", ... ,"here"),
     *          ...
     *      );
     *
     * or if the Gherkin table values comes from a examples table:
     *      | value    |
     *      | <field1> |
     *      | <field2> |
     *      | ...      |
     *      | <fieldN> |
     *
     *      Examples:
     *          | <field1> | <field2> | ... | <fieldN> |
     *          | value1   | value2   | ... | valueN   |
     *
     * the returned array should look like
     *      $data = array(
     *          "field1" => "value1",
     *          "field2" => "value2",
     *          ...
     *          "fieldN" => "valueN",
     *      );
     *
     * @param \Behat\Gherkin\Node\TableNode $table The Gherkin table to extract the values
     * @param array                         $data  If passed the values are concatenated/updated
     *
     * @return false|array
     */
    public function convertTableToArrayOfData( TableNode $table, array $data = null )
    {
        if( empty( $data ) )
            $data = array();

        // prepare given data
        $i = 0;
        $rows = $table->getRows();
        $headers = array_shift( $rows );   // this is needed to take the first row ( readability only )
        foreach ( $rows as $row )
        {
            $count = count( array_filter( $row ) );
            // check if the field is supposed to be empty
            // or it simply has only 1 element
            if (
                $count == 1
                && count( $row )
                && !method_exists( $table, "getCleanRows" )
            )
            {
                $count = 2;
            }

            $key = $row[0];
            switch( $count ){
            // case 1 is for the cases where there is an Examples table and it
            // gets the values from there, so the field name/id shold be on the
            // examples table (ex: "| <field_name> |")
            case 1:
                $value = $key;
                $aux = $table->getCleanRows();
                $k = ( count( $aux ) === count( array_keys( $table ) ) ) ? $i : $i + 1;

                $key = str_replace( array( '<', '>' ), array( '', '' ), $aux[$k][0] );
                break;

            // case 2 is the most simple case where "| field1 | as value 1 |"
            case 2:
                if ( count( $headers ) === 1 )
                {
                    $value = $row[0];
                }
                else
                {
                    $value = $row[1];
                }
                break;

            // this is for the cases where there are several values for the same
            // field (ex: author) and the gherkin table should look like
            default: $value = array_slice( $row, 1 );
                break;
            }
            $data[$key] = $value;
            $i++;
        }

        // if its empty return false otherwise return the array with data
        return empty( $data ) ? false : $data;
    }

    /**
     * Parameter given trough the BDD may come in so many ways like:
     * "Column 1"
     * "column1"
     * "Column 1 Row 2"
     * So it is needed a way to effectively get the number it's pretended for a
     * more accurate search through xpath
     *
     * @param string $string
     *
     * @return string
     */
    public function getNumberFromString( $string )
    {
        $max = strlen( $string );
        $result = "";

        // go through each character
        for ( $i = 0; $i < $max; $i++ )
        {
            // check if it is a number and add it to result
            if ( $string[$i] >= '0' && $string[$i] <= '9' )
            {
                $result .= $string[$i];
            }
            // if not verify if the number was already found
            else if ( $result !== "" )
            {
                return $result;
            }
        }

        return $result;
    }

    /**
     * Returns the path associated with $pageIdentifier
     *
     * @param string $pageIdentifier
     *
     * @return string
     *
     * @throws \RuntimeException If $pageIdentifier is not set
     */
    public function getPathByPageIdentifier( $pageIdentifier )
    {
        if ( !isset( $this->pageIdentifierMap[strtolower( $pageIdentifier )] ) )
        {
            throw new \RuntimeException( "Unknown page identifier '{$pageIdentifier}'." );
        }

        return $this->pageIdentifierMap[strtolower( $pageIdentifier )];
    }

    /**
     * Returns the path associated with the $fileSource
     *
     * @param string $file
     *
     * @return string
     *
     * @throws \RuntimeException If file is not set
     */
    public function getPathByFileSource( $file )
    {
        if ( !isset( $this->fileSource[$file] ) )
        {
            throw new \RuntimeException( "Unknown file '{$file}'." );
        }

        return $this->fileSource[$file];
    }

    /**
     * Returns $url without its query string
     *
     * @param string $url
     *
     * @return string
     */
    public function getUrlWithoutQueryString( $url )
    {
        if ( strpos( $url, '?' ) !== false )
        {
            $url = substr( $url, 0, strpos( $url, '?' ) );
        }

        return $url;
    }
}
