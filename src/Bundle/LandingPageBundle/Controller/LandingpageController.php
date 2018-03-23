<?php

namespace LandingPageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/")
 */
class LandingpageController extends Controller
{
    /**
     * Tech Leaders landing page
     * List of organizations
     * General info
     *
     * @Route(
     *     "/",
     *     name="landingpage",
     * )
     * @Template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('Entity:Organization');

        $organizations = $repo->findAll();

        $images = $em->getRepository('Entity:Image')->getAll();

        return [
            'organizations' => $organizations,
            'images' => $images
        ];
    }
}
