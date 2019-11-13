<?php

namespace App\Controller;

use App\Entity\About;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/")
 */
class LandingpageController extends AbstractController
{
    /**
     * @Route("/", name="landingpage")
     * @Template
     */
    public function indexAction()
    {
        return [
        ];
    }

    /**
     * @Route("/about", name="landingpage_about")
     * @Template
     */
    public function aboutAction()
    {
        $abouts = $this->getDoctrine()
            ->getRepository(About::class)
            ->findAll();

        return [
            'abouts' => $abouts,
        ];
    }
}
