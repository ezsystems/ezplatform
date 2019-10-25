<?php

namespace App\Context;

use Behat\Behat\Context\Context;
use EzSystems\Behat\Test\Factory\ElementFactoryInterface;
use EzSystems\Behat\Test\Factory\PageObjectFactoryInterface;

// rename to PageObjectBaseContext
class WebBrowserContext implements Context
{
    /** @var PageObjectFactoryInterface */
    protected $pageObjectFactory;

    /** @var ElementFactoryInterface  */
    protected $elementFactory;


}