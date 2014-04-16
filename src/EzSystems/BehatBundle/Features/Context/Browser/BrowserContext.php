<?php
/**
 * File containing the BrowserContext class.
 *
 * This class contains general feature context for Behat.
 *
 * @copyright Copyright (C) 1999-2014 eZ Systems AS. All rights reserved.
 * @license http://www.gnu.org/licenses/gpl-2.0.txt GNU General Public License v2
 * @version //autogentag//
 */

namespace EzSystems\BehatBundle\Features\Context\Browser;

use EzSystems\BehatBundle\Features\Context\FeatureContext as BaseFeatureContext;
use EzSystems\BehatBundle\Features\Context\Browser\SubContexts\AuthenticationContext;
use PHPUnit_Framework_Assert as Assertion;
use Behat\Behat\Context\Step;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Element\Element;
use Behat\Mink\Driver\GoutteDriver;
use Behat\Mink\Exception\UnsupportedDriverActionException as MinkUnsupportedDriverActionException;

/**
 * Browser interface helper context.
 */
class BrowserContext extends BaseFeatureContext implements BrowserInternalSentences
{
    /**
     * This will tell us which containers (design) to search, should be set by child classes.
     *
     * ex:
     * $mainAttributes = array(
     *      "content"   => "thisIsATag",
     *      "column"    => array( "class" => "thisIstheClassOfTheColumns" ),
     *      "menu"      => "//xpath/for/the[menu]",
     *      ...
     * );
     *
     * Is possible to define the specific xpath for a block, and all the other
     * options won't be processed, however this should ONLY be used when testing
     * content, otherwise if something changes on block it won't work
     *
     * @var array This will have a ( identifier => array )
     */
    protected $mainAttributes = array();

    /**
     * @var string
     */
    protected $priorSearchPhrase = '';

    public function __construct( array $parameters )
    {
        parent::__construct( $parameters );

        // add home to the page identifiers
        $this->pageIdentifierMap += array(
            'home'   => '/',
            'login'  => '/login',
            'logout' => '/logout'
        );

        // add main/base elements search
        $this->mainAttributes['main'] = array( 'tag' => 'body' );

        // sub contexts
        $this->useContext( 'Authentication', new AuthenticationContext() );
    }

    /**
     * This method works is a complement to the $mainAttributes var
     *
     * @param  string $block This should be an identifier for the block to use
     *
     * @return string
     *
     * @see $this->mainAttributes
     */
    protected function makeXpathForBlock( $block = 'main' )
    {
        $parameter = ( isset( $this->mainAttributes[strtolower( $block )] ) ) ?
            $this->mainAttributes[strtolower( $block )] :
            NULL;

        Assertion::assertNotNull( $parameter, "Element {$block} is not defined" );

        $xpath = $this->mainAttributes[strtolower( $block )];
        // check if value is a composed array
        if ( is_array( $xpath ) )
        {
            // if there is an xpath defined look no more!
            if ( isset( $xpath['xpath'] ) )
            {
                return $xpath['xpath'];
            }

            $nuXpath = "";
            // verify if there is a tag
            if ( isset( $xpath['tag'] ) )
            {
                if ( strpos( $xpath['tag'], "/" ) === 0 || strpos( $xpath['tag'], "(" ) === 0 )
                {
                    $nuXpath = $xpath['tag'];
                }
                else
                {
                    $nuXpath = "//" . $xpath['tag'];
                }

                unset( $xpath['tag'] );
            }
            else
            {
                $nuXpath = "//*";
            }

            foreach ( $xpath as $key => $value )
            {
                switch ( $key )
                {
                    case "text":
                        $att = "text()";
                        break;
                    default:
                        $att = "@$key";
                }
                $nuXpath .= "[contains($att, {$this->literal( $value )})]";
            }

            return $nuXpath;
        }

        //  if the string is an Xpath
        if ( strpos( $xpath, "/" ) === 0 || strpos( $xpath, "(" ) === 0 )
        {
            return $xpath;
        }

        // if xpath is an simple tag
        return "//$xpath";
    }

    /**
     * With this function we get a centralized way to define what are the possible
     * tags for a type of data and return them as a xpath search
     *
     * @param  string $type Type of text (ie: if header/title, or list element, ...)
     *
     * @return string Xpath string for searching elements inside those tags
     *
     * @throws PendingException If the $type isn't defined yet
     */
    protected function getTagsFor( $type )
    {
        switch ( strtolower( $type ) )
        {
            case "topic":
            case "header":
            case "title":
                return array( "h1", "h2", "h3" );
            case "list":
                return array( "li" );
            case "input":
                return array( "input", "select", "textarea", );
        }

        throw new PendingException( "Tag's for '$type' type not defined" );
    }

    /**
     * This should be seen as a complement to self::getTagsFor() where it will
     * get the respective tags from there and will make a valid Xpath string with
     * all OR's needed
     *
     * @param array  $tags  Array of tags strings (ex: array( "a", "p", "h3", "table" ) )
     * @param string $xpath String to be concatenated to each tag
     *
     * @return string
     */
    protected function concatTagsWithXpath( array $tags, $xpath = null )
    {
        $finalXpath = "";
        for ( $i = 0; !empty( $tags[$i] ); $i++ )
        {
            $finalXpath .= "//{$tags[$i]}$xpath";
            if ( !empty( $tags[$i + 1] ) )
            {
                $finalXpath .= " | ";
            }
        }

        return $finalXpath;
    }

    /**
     * This is a simple shortcut for
     * $this->getSession()->getPage()->getSelectorsHandler()->xpathLiteral()
     *
     * @param string $text
     */
    protected function literal( $text )
    {
        return $this->getSession()->getSelectorsHandler()->xpathLiteral( $text );
    }

    /**
     * This function is used for testing if the driver supports redirect interception
     * for the "I follow the redirection" step
     *
     * @throws UnsupportedDriverActionException
     */
    protected function canIntercept()
    {
        $driver = $this->getSession()->getDriver();
        if ( !$driver instanceof GoutteDriver )
        {
            throw new MinkUnsupportedDriverActionException(
                'You need to tag the scenario with ' .
                '"@mink:goutte" or "@mink:symfony". ' .
                'Intercepting the redirections is not ' .
                'supported by %s', $driver
            );
        }
    }

    /**
     * Find the links passed, assert they exist in the specified place
     *
     * @param array  $links The links to be asserted
     * @param string $where The place where should search for the links
     *
     * @todo verify if the links are for objects
     * @todo check if it has a different url alias
     */
    protected function checkLinksForContentObjects( array $links, $where )
    {
        $base = $this->makeXpathForBlock( $where );
        foreach ( $links as $link )
        {
            Assertion::assertNotNull( $link, "Missing link for searching on table" );

            $literal = $this->literal( $link );
            $el = $this->getSession()->getPage()->find(
                "xpath",
                "$base//a[contains( text(),$literal )][@href]"
            );

            Assertion::assertNotNull( $el, "Couldn't find a link for object '$link'" );
        }
    }

    /**
     * Check existence of links
     *
     * @param array         $links
     * @param NodeElement[] $available
     */
    protected function checkLinksExistence( array $links, array $available )
    {
        // verify if every required link is in available
        foreach ( $links as $link )
        {
            $name = $link;
            $url = str_replace( ' ', '-', $name );

            $i = 0;
            while (
                !empty( $available[$i] )
                && strpos( $available[$i]->getattribute( "href" ), $url ) === false
                && strpos( $available[$i]->getText(), $name ) === false
            )
                $i++;

            $test = !null;
            if ( empty( $available[$i] ) )
            {
                $test = null;
            }

            // check if the link was found or the $i >= $count
            Assertion::assertNotNull( $test, "Couldn't find '$name' link" );
        }
    }

    /**
     * Checks if links show up in the following order
     * Notice: if there are 3 links and we omit the middle link it will also be
     *  correct. It only checks order, not if there should be anything in
     *  between them
     *
     * @param array         $links
     * @param NodeElement[] $available
     */
    protected function checkLinkOrder( array $links, array $available )
    {
        $i = $passed = 0;
        $last = '';
        foreach ( $links as $link )
        {
            $name = $link;
            $url = str_replace( ' ', '-', $name );

            // find the object
            while (
                !empty( $available[$i] )
                && strpos( $available[$i]->getAttribute( "href" ), $url ) === false
                && strpos( $available[$i]->getText(), $name ) === false
            )
                $i++;

            $test = !null;
            if ( empty( $available[$i] ) )
            {
                $test = null;
            }

            // check if the link was found or the $i >= $count
            Assertion::assertNotNull( $test, "Couldn't find '$name' after '$last'" );

            $passed++;
            $last = $name;
        }

        Assertion::assertEquals(
            count( $links ),
            $passed,
            "Expected to evaluate '{count( $links )}' links evaluated '{$passed}'"
        );
    }

    /**
     * Verifies if a row as the expected columns, position of columns can be added
     * for a more accurated assertion
     *
     * @param \Behat\Mink\Element\NodeElement  $row              Table row node element
     * @param string[]                         $columns          Column text to assert
     * @param string[]|int[]                   $columnsPositions Columns positions in int or string (number must be in string)
     *
     * @return boolean
     */
    protected function assertTableRow( NodeElement $row, array $columns, array $columnsPositions = null )
    {
        // find which kind of column is in this row
        $elType = $row->find( 'xpath', "/th" );
        $type = ( empty( $elType ) ) ? '/td' : '/th';

        $max = count( $columns );
        for ( $i = 0; $i < $max; $i++ )
        {
            $position = "";
            if ( !empty( $columnsPositions[$i] ) )
            {
                $position = "[{$this->getNumberFromString( $columnsPositions[$i] )}]";
            }

            $el = $row->find( "xpath", "$type$position" );

            // check if match with expected if not return false
            if ( $el === null || $columns[$i] !== $el->getText() )
            {
                return false;
            }
        }

        // if we're here then it means all have ran as expected
        return true;
    }

    /**
     * Find a(all) table row(s) that match the column text
     *
     * @param string        $text       Text to be found
     * @param string|int    $column     In which column the text should be found
     * @param string        $tableXpath If there is a specific table
     *
     * @return Behat\Mink\Element\NodeElement[]
     */
    protected function getTableRow( $text, $column = null, $tableXpath = null )
    {
        // check column
        if ( !empty( $column ) )
        {
            if ( is_integer( $column ) )
            {
                $columnNumber = "[$column]";
            }
            else
            {
                $columnNumber = "[{$this->getNumberFromString( $column )}]";
            }
        }
        else
        {
            $columnNumber = "";
        }

        // get all possible elements
        $elements = array_merge(
            $this->getSession()->getPage()->findAll( "xpath", "$tableXpath//tr/th" ),
            $this->getSession()->getPage()->findAll( "xpath", "$tableXpath//tr/td" )
        );

        $foundXpath = array();
        $total = count( $elements );
        $i = 0;
        while ( $i < $total )
        {
            if ( strpos( $elements[$i]->getText(), $text ) !== false )
            {
                $foundXpath[] = $elements[$i]->getParent();
            }

            $i++;
        }

        return $foundXpath;
    }

    /**
     * Verifies if the element has 'special' configuration on a attribute (default -> style)
     *
     * @param \Behat\Mink\Element\NodeElement  $el              The element that we want to test
     * @param string                           $characteristic  Verify a specific characteristic from attribute
     * @param string                           $attribute       Verify a specific attribute
     *
     * @return boolean
     */
    protected function assertElementEmphasized( NodeElement $el, $characteristic = null, $attribute = "style" )
    {
        // verify it has the attribute we're looking for
        if ( !$el->hasAttribute( $attribute ) )
        {
            return false;
        }

        // get the attribute
        $attr = $el->getAttribute( $attribute );

        // check if want to test specific characteristic and if it is present
        if ( !empty( $characteristic ) && strpos( $attr, $characteristic ) === false )
        {
            return false;
        }

        // if we're here it is emphasized
        return true;
    }

    /**
     * Find field element
     * This is a complement to the normal search, because in some cases the
     * label has no "for" attribute, so the normal search won't find it. So this
     * will try to find an input right after a label with $field
     *
     * @param string $field Can be id, name, label, value
     *
     * @return Behat\Mink\Element\NodeElement
     */
    protected function findFieldElement( $field )
    {
        $page = $this->getSession()->getPage();

        // attempt to find field through id, name, or label
        $fieldElement = $page->findField( $field );
        if ( empty( $fieldElement ) )
        {
            // if field wasn't found, and there is an label for that, we will
            // attempt to find the next input after the label
            $fieldElement = $page->find(
                "xpath",
                "//*[self::" .
                implode( " or self::", $this->getTagsFor( 'input' ) )
                . "][preceding::label[contains( text(), "
                . $this->literal( $field )
                . " )]]"
            );
        }

        Assertion::assertNotNull( $fieldElement, "Couldn't find '$field' field" );

        return $fieldElement;
    }

    /**
     * Browser fill field is more than the TraversableElement::fillField since a
     * simple Element::setValue won't do the job in several cases
     *
     * @param Behat\Mink\Element\Element $field The html input node
     * @param mixed $value The value that should be setted onto the field
     */
    protected function browserFillField( Element $field, $value = null )
    {
        $typeAttributeOrTag = $field->getAttribute( "type" );
        if ( empty( $typeAttributeOrTag ) )
        {
            $typeAttributeOrTag = $field->getTagName();
        }

        switch ( strtolower( $typeAttributeOrTag ) ) {
        case "select":
            $field->selectOption( $value );
            break;

        case "radio":
            // Goutte can't use click on a non submit so it needs to set
            // the value for the radio button
            if ( $this->getSession()->getDriver() instanceof GoutteDriver )
            {
                if ( empty( $value ) )
                {
                    $value = $field->getAttribute( 'value' );
                }

                $field->setValue( $value );
            }
            else
            {
                $field->click();
            }
            break;

        case "checkbox":
            if ( $value && $value !== "false" )
            {
                $field->check();
            }
            else
            {
                $field->uncheck();
            }
            break;

        case "file":
            $field->attachFile( $value );
            break;

        // most cases simple set value will do
        default:
            $field->setValue( $value );
        }
    }

    /**
     * Find and return the row (<tr>) where the passed element is
     * This is useful when you intend to know if another element is in the same
     * row
     *
     * @param \Behat\Mink\Element\Element $element The element in the intended row
     *
     * @return \Behat\Mink\Element\Element The <tr> element node
     */
    protected function findRow( Element $element )
    {
        $inicialTag = $element->getTagName();

        while (
            strtolower( $element->getTagName() ) !== "tr"
            && strtolower( $element->getTagName() ) !== "body"
        )
        {
            $element = $element->getParent();
        }

        Assertion::assertEquals(
            strtolower( $element->getTagName() ),
            "tr",
            "Couldn't find a parent of '$inicialTag' that is a table row"
        );

        return $element;
    }

    /**
     * In a list of elements returns a certain element (found through xpath) that
     * is after a specific element (that is also found through xpath)
     *
     * <code>
     * findElementAfterElement(
     *      $arrayWithAllCellsOfARow,
     *      $xpathForALabel
     *      $xpathForAnInput
     * );
     * </code>
     *
     * @param array $elements
     * @param type $firstXpath
     * @param type $secondXpath
     *
     * @return type
     */
    protected function findElmentAfterElement( array $elements, $firstXpath, $secondXpath )
    {
        $foundFirstXpath = false;
        foreach ( $elements as $element )
        {
            // choose what xpath to use
            if ( !$foundFirstXpath )
            {
                $xpath = $firstXpath;
            }
            else
            {
                $xpath = $secondXpath;
            }

            $foundElement = $element->find( "xpath", $xpath );

            // element found, if first start to look for the second one
            // if second, than return this one
            if ( !empty( $foundElement) )
            {
                if ( !$foundFirstXpath )
                {
                    $foundFirstXpath = true;
                }
                else
                {
                    return $foundElement;
                }
            }
        }

        Assertion::assertNotNull( null, "Couldn't find an element with '$secondXpath' xpath after '$firstXpath' xpath" );
    }

    /**
     * After this comment are the Browser sentences implementation
     *
     * @see BrowserInternalSentences
     */

    public function checkOption( $option )
    {
        $page = $this->getSession()->getPage();
        $fieldElement = $page->findField( $option );
        Assertion::assertNotNull( $fieldElement, "Couldn't find '$option' checkbox" );

        $type = $fieldElement->getAttribute( 'type' );

        // this is needed for the cases where are checkboxes and radio's
        // side by side, for main option the radio and the extra being the
        // checkboxes values
        if ( strtolower( $type ) !== 'checkbox' )
        {
            $value = $fieldElement->getAttribute( 'value' );
            $fieldElement = $page->find( "xpath", "//input[@type='checkbox' and @value='$value']" );
        }

        Assertion::assertNotNull( $fieldElement, "Couldn't find a checkbox with '$value' value" );

        $this->browserFillField( $fieldElement, true );
    }

    public function iClickAtButton( $button )
    {
        return array(
            new Step\When( "I press \"{$button}\"" )
        );
    }

    public function onPageSectionIClickAtButton( $pageSection, $button )
    {
        $base = $this->makeXpathForBlock( $pageSection );

        $literal = $this->literal( $button );
        $el = $this->getSession()->getPage()->find(
            "xpath", "$base//button" .
            "[contains(text(),{$literal}) " .
            "or contains(@id,{$literal}) " .
            "or contains(@class, {$literal}) " .
            "or contains(@name,{$literal})]"
        );

        Assertion::assertNotNull( $el, "Couldn't find '$button' button" );

        $el->click();
    }

    public function iClickAtLink( $link )
    {
        return array(
            new Step\When( "I follow \"{$link}\"" )
        );
    }

    public function onPageSectionIClickAtLink( $pageSection, $link )
    {
        $base = $this->makeXpathForBlock( $pageSection );

        $literal = $this->literal( $link );
        $el = $this->getSession()->getPage()->find(
            "xpath",
            "$base//a[@href][contains( text(), $literal ) or contains( @href, $literal )]"
        );

        Assertion::assertNotNull( $el, "Couldn't find '$link' link" );

        $el->click();
    }

    public function iFillFormWith( TableNode $table )
    {
        foreach ( $this->convertTableToArrayOfData( $table ) as $field => $value )
        {
            // fill the form
            $this->browserFillField(
                $this->findFieldElement( $field ),
                $value
            );
        }
    }

    public function iGoToThe( $pageIdentifier )
    {
        return array(
            new Step\When( 'I am on "' . $this->getPathByPageIdentifier( $pageIdentifier ) . '"' ),
        );
    }

    public function iSearchFor( $searchPhrase )
    {
        $session = $this->getSession();
        $searchField = $session->getPage()->findById( 'site-wide-search-field' );

        Assertion::assertNotNull( $searchField, 'Search field not found.' );

        $searchField->setValue( $searchPhrase );

        // Ideally, using keyPress(), but doesn't work since no keypress handler exists
        // http://sahi.co.in/forums/discussion/2717/keypress-in-java/p1
        //     $searchField->keyPress( 13 );
        //
        // Using JS instead:
        // Note:
        //     $session->executeScript( "$('#site-wide-search').submit();" );
        // Gives:
        //     error:_call($('#site-wide-search').submit();)
        //     SyntaxError: missing ) after argument list
        //     Sahi.ex@http://<hostname>/_s_/spr/concat.js:3480
        //     @http://<hostname>/_s_/spr/concat.js:3267
        // Solution: Encapsulating code in a closure.
        // @todo submit support where recently added to MinkCoreDriver, should us it when the drivers we use support it
        try
        {
            $session->executeScript( "(function(){ $('#site-wide-search').submit(); })()" );
        }
        catch ( MinkUnsupportedDriverActionException $e )
        {
            // For drivers not able to do javascript we assume we can click the hidden button
            $searchField->getParent()->findButton( 'SearchButton' )->click();
        }

        // Store for reuse in result page
        $this->priorSearchPhrase = $searchPhrase;
    }

    public function iSelect( $option )
    {
        $elements = $this->getSession()->getPage()->findAll( "xpath", "//select" );

        Assertion::assertNotNull( $elements, "Couldn't find any select element" );
        Assertion::assertEquals( count( $elements ), 1, "Found more than 1 select element" );

        $this->browserFillField( $elements[0], $option );
    }

    public function iSelectRadioButon( $label )
    {
        $el = $this->getSession()->getPage()->findField( $label );

        Assertion::assertNotNull( $el, "Couldn't find a radio input with '$label'" );

        $this->browserFillField( $el, true );
    }

    public function iFollowTheRedirection()
    {
        $this->canIntercept();
        $client = $this->getSession()->getDriver()->getClient();
        $client->followRedirects( true );
        $client->followRedirect();
    }

    public function iSeeButton( $button )
    {
        Assertion::assertNotNull(
            $this->getSession()->getPage()->findButton( $button ),
            "Could not find '$button' button."
        );
    }

    public function iSeeCheckboxFieldWithLabel( $label )
    {
        $elements = $this->getSession()->getPage()->findAll(
            "xpath",
            "//input[@type = 'checkbox']/.."
        );

        Assertion::assertNotEquals( count( $elements ), 0, "Coudn't find any checkbox" );

        $found = false;
        for ( $i = 0; $i < count( $elements ) && !$found; $i++ )
        {
            if ( strpos( $elements[$i]->getText(), $label ) !== false )
            {
                $found = true;
            }
        }

        // assert that it was found
        Assertion::assertEquals(
            true,
            $found,
            "Couldn't find a checkbox with label '$label'"
        );
    }

    public function iSeeElement( $element )
    {
        $this->onPageSectionISeeElement( 'main', $element );
    }

    public function onPageSectionISeeElement( $pageSection, $element )
    {
        $base = $this->makeXpathForBlock( $pageSection );

        // an element can't be search through content, so lets find through
        // id, class, name, src or href
        $literal = $this->literal( $element );
        $el = $this->getSession()->getPage()->find(
            "xpath",
            "$base//*["
            . "contains( @id, $literal )"
            . "or contains( @class, $literal )"
            . "or contains( @name, $literal )"
            . "or contains( @src, $literal )"
            . "or contains( @href, $literal )"
            . "]"
        );

        Assertion::assertNotNull( $el, "Expected element '$element' not found" );
    }

    public function iSeeTotalElements( $total, $objectType )
    {
        $objectListTable = $this->getSession()->getPage()->find(
            'xpath',
            '//table[../h1 = "' . $objectType . ' list"]'
        );

        Assertion::assertNotNull(
            $objectListTable,
            'Could not find listing table for ' . $objectType
        );

        Assertion::assertCount(
            $total + 1,
            $objectListTable->findAll( 'css', 'tr' ),
            'Found incorrect number of table rows.'
        );
    }

    public function iDonTSeeElement( $element )
    {
        $this->onPageSectionIDontSeeElement( 'main', $element );
    }

    public function onPageSectionIDontSeeElement( $pageSection, $element )
    {
        $base = $this->makeXpathForBlock( $pageSection );

        // an element can't be search through content, so lets find through
        // id, class, name, src or href
        $literal = $this->literal( $element );
        $el = $this->getSession()->getPage()->find(
            "xpath",
            "$base//*["
            . "contains( @id, $literal )"
            . "or contains( @class, $literal )"
            . "or contains( @name, $literal )"
            . "or contains( @src, $literal )"
            . "or contains( @href, $literal )"
            . "]"
        );

        Assertion::assertEmpty( $el, "Unexpected element '$element' found" );
    }

    public function iSeeWarning( $warning )
    {
        $el = $this->getSession()->getPage()->find(
            "xpath",
            "//*[contains( @class, 'warning' ) or contains( @class, 'error' )]"
            . "//*[contains( text(), " . $this->literal( $warning ) . " )]"
        );

        Assertion::assertNotNull( $el, "Couldn't find error message '$warning'" );
        Assertion::assertContains( $warning, $el->getText(), "Couldn't find error message '$warning'" );
    }

    public function iSeeText( $text )
    {
        $this->onPageSectionISeeAKey( 'main', $text );
    }

    public function onPageSectionISeeText( $pageSection, $text )
    {
        $base = $this->makeXpathForBlock( $pageSection );

        $literal = $this->literal( $text );

        // get the key
        // using 'contains' will help for the cases where there are spaces of \n on
        // the tag, and if used '=' it wouldn't be found
        $el = $this->getSession()->getPage()->find( "xpath", "$base//*[contains( text(), $literal )]" );

        Assertion::assertNotNull( $el, "Couldn't find '$text' text" );
        Assertion::assertEquals( trim( $el->getText() ), $text, "Couldn't find '$text' text" );
    }

    public function iSeeKeyWithValue( $key, $value )
    {
        $el = $this->getSession()->getPage()->findAll(
            "xpath",
            "//*[contains( text(), " . $this->literal( $key ) . " )]"
        );

        Assertion::assertNotNull( $el, "Couldn't find tag with '$key' text" );

        $found = false;
        for ( $i = 0; $i < count( $el ) && !$found; $i++ )
        {
            $found = strpos( $el[$i]->getParent()->getText(), $value );
        }

        Assertion::assertNotEquals( false, $found, "Couldn't find a key '$key' with value '$value'" );
    }

    public function iSeeLink( $link )
    {
        $this->onPageSectionISeeLink( 'main', $link );
    }

    public function onPageSectionISeeLink( $pageSection, $link )
    {
        $base = $this->makeXpathForBlock( $pageSection );
        Assertion::assertNotNull( $link, "Missing link for searching on table" );

        $literal = $this->literal( $link );
        $el = $this->getSession()->getPage()->find(
            "xpath",
            "$base//a[contains( text(), $literal )][@href]"
        );
        Assertion::assertNotNull( $el, "Couldn't find a link for object '$link'" );
    }

    public function iSeeLinks( TableNode $table )
    {
        $this->onSomeSeeIPlaceLinks( 'main', $table );
    }

    public function onPageSectionISeeLinks( $pageSection, TableNode $table )
    {
        $base = $this->makeXpathForBlock( $pageSection );
        // get all links
        $available = $this->getSession()->getPage()->findAll( "xpath", "$base//a[@href]" );

        $rows = $table->getRows();
        array_shift( $rows );   // this is needed to take the first row ( readability only )
        // remove links from embeded arrays
        $links = array();
        foreach ( $rows as $row )
        {
            $links[] = $row[0];
        }

        // and finaly verify their existence
        $this->checkLinksExistence( $links, $available );
    }

    public function iSeeLinksForContentObjects( TableNode $table )
    {
        $this->onPageSectionISeeTheLinksForContentObjects( 'main', $table );
    }

    public function onPageSectionISeeTheLinksForContentObjects( $pageSection, TableNode $table )
    {
        $rows = $table->getRows();
        array_shift( $rows );   // this is needed to take the first row ( readability only )

        $links = $parents = array();
        foreach ( $rows as $row )
        {
            if ( count( $row ) >= 2 )
            {
                list( $links[], $parents[] ) = $row;
            }
            else
            {
                $links[] = $row[0];
            }
        }

        // check links
        $this->checkLinksForContentObjects( $links, $pageSection );

        // to end the assertion, we need to verify parents (if specified)
//        if ( !empty( $parents ) )
//            $this->checkLinkParents( $links, $parents );
    }

    public function iSeeLinksInFollowingOrder( TableNode $table )
    {
        $this->onPageSectionISeeLinksInFollowingOrder( 'main', $table );
    }

    public function onPageSectionISeeLinksInFollowingOrder( $pageSection, TableNode $table )
    {
        $base = $this->makeXpathForBlock( $pageSection );
        // get all links
        $available = $this->getSession()->getPage()->findAll( "xpath", "$base//a[@href]" );

        $rows = $table->getRows();
        array_shift( $rows );   // this is needed to take the first row ( readability only )
        // make link and parent arrays:
        $links = array();
        foreach ( $rows as $row )
        {
            $links[] = $row[0];
        }

        // now verify the link order
        $this->checkLinkOrder( $links, $available );
    }

    public function onPageSectionISeeTotalLinks( $pageSection, $totalLinks )
    {
        $base = $this->makeXpathForBlock( $pageSection );

        $allLinks = $this->getSession()->getPage()->findAll( "xpath", "{$base}//a[@href]" );

        Assertion::assertEquals( count( $allLinks ), $totalLinks );
    }

    public function iSeeLinksInTag( TableNode $table )
    {
        $session = $this->getSession();
        $rows = $table->getRows();
        array_shift( $rows );   // this is needed to take the first row ( readability only )
        foreach ( $rows as $row )
        {
            // prepare data
            Assertion::assertEquals(
                count( $row ), 2,
                "The table should be have array with link and tag"
            );
            list( $link, $type ) = $row;

            // make xpath
            $literal = $this->literal( $link );
            $xpath = $this->concatTagsWithXpath(
                $this->getTagsFor( $type ),
                "//a[@href and text() = $literal]"
            );

            $el = $session->getPage()->find( "xpath", $xpath );

            Assertion::assertNotNull( $el, "Couldn't find a link with '$link' text" );
        }
    }

    public function iDontSeeLink( $link )
    {
        $this->onPageSectionIDonTSeeALink( 'main', $link );
    }

    public function onPageSectionIDonTSeeLink( $pageSection, $link )
    {
        $xpath = $this->makeXpathForBlock( $pageSection );

        $literal = $this->literal( $link );

        $el = $this->getSession()->getPage()->find(
            "xpath",
            $xpath . "//a[contains( text(), $literal ) or contains( @href, $literal )][@href]"
        );

        Assertion::assertNotNull( $el, "Link '$link' not found" );
    }

    public function iDonTSeeLinks( TableNode $table )
    {
        $this->onPageSectionIDonTSeeTheLinks( 'main', $table );
    }

    public function onPageSectionIDonTSeeLinks( $pageSection, TableNode $table )
    {
        $rows = $table->getRows();
        array_shift( $rows );   // this is needed to take the first row ( readability only )

        $base = $this->makeXpathForBlock( $pageSection );
        foreach ( $rows as $row )
        {
            $link = $row[0];
            $literal = $this->literal( $link );
            $el = $this->getSession()->getPage()->find( "xpath", "$base//a[text() = $literal][@href]" );

            Assertion::assertNull( $el, "Unexpected link found" );
        }
    }

    public function iSeeMessage( $text )
    {
        return array( new Step\Then( "I should see \"$text\"" ) );
    }

    public function iDonTSeeMessage( $message )
    {
        $literal = $this->literal( $message );
        $el = $this->getSession()->getPage()->find( "xpath", "//*[@text = $literal]" );

        Assertion::assertNull( $el, "Message $literal found" );
    }

    public function iSeePage( $pageIdentifier )
    {
        $currentUrl = $this->getUrlWithoutQueryString( $this->getSession()->getCurrentUrl() );

        $expectedUrl = $this->locatePath( $this->getPathByPageIdentifier( $pageIdentifier ) );

        Assertion::assertEquals(
            $expectedUrl,
            $currentUrl,
            "Unexpected URL of the current site. Expected: '$expectedUrl'. Actual: '$currentUrl'."
        );
    }

    public function iSeeSearchResults( $total )
    {
        $resultCountElement = $this->getSession()->getPage()->find( 'css', 'div.feedback' );

        Assertion::assertNotNull(
            $resultCountElement,
            'Could not find result count text element.'
        );

        Assertion::assertEquals(
            "Search for \"{$this->priorSearchPhrase}\" returned {$total} matches",
            $resultCountElement->getText()
        );
    }

    public function iSeeTableWith( TableNode $table )
    {
        $rows = $table->getRows();
        $headers = array_shift( $rows );

        $max = count( $headers );
        $mainHeader = array_shift( $headers );
        foreach ( $rows as $row )
        {
            $mainColumn = array_shift( $row );
            $foundRows = $this->getTableRow( $mainColumn, $mainHeader );

            $found = false;
            $maxFound = count( $foundRows );
            for ( $i = 0; $i < $maxFound && !$found; $i++ )
            {
                if ( $this->assertTableRow( $foundRows[$i], $row, $headers ) )
                {
                    $found = true;
                }
            }

            $message = "Couldn't find row with elements: '" . implode( ",", array_merge( array( $mainColumn ), $row ) ) . "'";
            Assertion::assertTrue( $found, $message );
        }
    }

    public function iSeeTextEmphasized( $text )
    {
        $this->onPageSectionISeeTextEmphasized( 'main', $text );
    }

    public function onPageSectionISeeTextEmphasized( $pageSection, $text )
    {
        // first find the text
        $base = $this->makeXpathForBlock( $pageSection );
        $el = $this->getSession()->getPage()->findAll( "xpath", "$base//*[contains( text(), {$this->literal( $text )} )]" );
        Assertion::assertNotNull( $el, "Coudn't find text '$text' at '$pageSection' content" );

        // verify only one was found
        Assertion::assertEquals( count( $el ), 1, "Expecting to find '1' found '" . count( $el ) . "'" );

        // finaly verify if it has custom charecteristics
        Assertion::assertTrue(
            $this->assertElementEmphasized( $el[0] ),
            "The text '$text' isn't emphasized"
        );
    }

    public function iSeeTitle( $title )
    {
        $literal = $this->literal( $title );
        $xpath = $this->concatTagsWithXpath(
            $this->getTagsFor( "title" ),
            "[text() = {$literal} or .//*[text() = {$literal}]]"
        );

        $el = $this->getSession()->getPage()->find( "xpath", $xpath );

        // assert that message was found
        Assertion::assertNotNull( $el, "Could not find '$title' title." );
        Assertion::assertContains(
            $title,
            $el->getText(),
            "Couldn't find '$title' title in '{$el->getText()}'"
        );
    }

    /**
     * @todo Check selenium behaviour
     */
    public function onPageSectionISeeVideo( $pageSection, $video )
    {
        $base = $this->makeXpathForBlock( $pageSection );

        $videoSource = $this->getPathByFileSource( $video );
        $el = $this->getSession()->getPage()->find( "xpath", "{$base}//video//source" );

        Assertion::assertNotNull( $el, "Video object {$video} not found" );
        Assertion::assertEquals( $el->getAttribute( 'src' ), $videoSource );
    }

    public function iShouldBeRedirectedTo( $redirectTarget )
    {
        $redirectForm = $this->getSession()->getPage()->find( 'css', 'form[name="Redirect"]' );

        Assertion::assertNotNull( $redirectForm, 'Missing redirect form.' );
        Assertion::assertEquals(
            $redirectTarget,
            $redirectForm->getAttribute( 'action' )
        );
    }

    /**
     * @Then /^(?:|I )want (?:|a )dump of (?:|the )page$/
     */
    public function iWantDumpOfThePage()
    {
        $this->printDebug( $this->getSession()->getPage()->getContent() );
    }
}
