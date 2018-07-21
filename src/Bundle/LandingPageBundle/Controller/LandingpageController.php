<?php

namespace LandingPageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
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
     *     methods={"GET"}
     * )
     * @Template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('Entity:Organization');

        $organizations = $repo->findBy(['isAccepted' => true]);

        $images = $em->getRepository('Entity:Image')->getAll();

        return [
            'organizations' => $organizations,
            'images' => $images
        ];
    }
}
