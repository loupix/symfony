<?php

namespace Shoefony\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
	/**
     * @Route("/", name="shoefony_admin_homepage")
     */
    public function indexAction()
    {
        return $this->render('ShoefonyAdminBundle:Default:baseAdmin.html.twig');
    }
}
