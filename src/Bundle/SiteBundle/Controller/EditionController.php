<?php

namespace SiteBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Entity\Edition;

/**
 * Homepage
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
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
     * @Template("SiteBundle:homepage:index.html.twig")
     */
    public function showAction(Edition $edition)
    {
        $em = $this->getDoctrine()->getManager();

        $subdomain = $this->get('request_stack')->getCurrentRequest()
            ->attributes->get('subdomain');

        $mentors = $em->getRepository('Entity:Mentor')
            ->getFromEdition($edition->getId());

        $partners = $em->getRepository('Entity:Partner')
            ->getFromEdition($edition->getId());

        $sponsors = $em->getRepository('Entity:Sponsor')
            ->getFromEdition($edition->getId());

        $about = $em->getRepository('Entity:About')
            ->getAll();

        return [
            'mentors' => $mentors,
            'sponsors' => $sponsors,
            'partners' => $partners,
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
     * @Template("SiteBundle:homepage:mentors.html.twig")
     */
    public function mentorsAction(Edition $edition)
    {
        $em = $this->getDoctrine()->getManager();

        $mentors = $em->getRepository('Entity:Mentor')
            ->getFromEdition($edition->getId());

        $mentorsFaq = $em->getRepository('Entity:MentorFaq')
            ->getAll();

        return [
            'mentors' => $mentors,
            'mentorsFaq' => $mentorsFaq,
            'edition' => $edition,
        ];
    }
}
