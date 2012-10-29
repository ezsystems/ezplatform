<?php

namespace eZ\Publish\Bundles\CookBookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('eZPublishBundlesCookBookBundle:Default:index.html.twig', array('name' => $name));
    }
}
