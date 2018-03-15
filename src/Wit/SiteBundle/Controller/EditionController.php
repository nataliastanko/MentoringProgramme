<?php

namespace Wit\SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wit\Program\Admin\EditionBundle\Entity\Edition;

/**
 * Homepage
 *
 * @Route("/edition")
 */
class EditionController extends Controller
{
    /**
     * @Route(
     *     "/{edition}",
     *     name="edition_show",
     *     requirements=
     *     {
     *         "edition": "\d+",
     *     }
     * )
     * @Method("GET")
     * @Template("WitSiteBundle:Homepage:index.html.twig")
     */
    public function showAction(Edition $edition)
    {
        $em = $this->getDoctrine()->getManager();

        $mentors = $em->getRepository('WitProgramAdminEditionBundle:Mentor')
            ->getFromEdition($edition->getId());

        $partners = $em->getRepository('WitProgramAdminEditionBundle:Partner')
            ->getFromEdition($edition->getId());

        $sponsors = $em->getRepository('WitProgramAdminEditionBundle:Sponsor')
            ->getFromEdition($edition->getId());

        $about = $em->getRepository('WitProgramAdminSiteContentBundle:About')
            ->getAll();

        $images = $em->getRepository('WitProgramAdminGalleryBundle:Image')
            ->getAll();

        return [
            'mentors' => $mentors,
            'sponsors' => $sponsors,
            'partners' => $partners,
            'images' => $images,
            'edition' => $edition,
            'about' => $about,
        ];
    }

    /**
     * @Route(
     *     "/{edition}/mentors",
     *     name="edition_mentors",
     *     requirements=
     *     {
     *         "edition": "\d+",
     *     }
     * )
     * @Method("GET")
     * @Template("WitSiteBundle:Homepage:mentors.html.twig")
     */
    public function mentorsAction(Edition $edition)
    {
        $em = $this->getDoctrine()->getManager();

        $mentors = $em->getRepository('WitProgramAdminEditionBundle:Mentor')
            ->getFromEdition($edition->getId());

        $mentorsFaq = $em->getRepository('WitProgramAdminSiteContentBundle:MentorFaq')
            ->getAll();

        return [
            'mentors' => $mentors,
            'mentorsFaq' => $mentorsFaq,
            'edition' => $edition,
        ];
    }
}
