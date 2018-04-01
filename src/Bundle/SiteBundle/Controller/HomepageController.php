<?php

namespace SiteBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Entity\Edition;
use Annotation\Controller\SectionEnabled;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/")
 */
class HomepageController extends Controller
{
    /**
     * @TODO
     * https://github.com/Atlantic18/DoctrineExtensions/issues?utf8=%E2%9C%93&q=translatable
     * https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/translatable.md
     * slow query lazy loading, use joins (join mentors [and other types of objects] with translatable columns)
     * "May impact your application performance since it does an additional query for translation if loaded without query hint"
     * "ORM query can use hint to translate all records without issuing additional queries"
     * @TODO use query hint
     * https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/translatable.md#using-orm-query-hint
     *
     *
     * Forward to the latest edition.
     *
     * @Route(
     *     "/",
     *     name="homepage",
     *     defaults=
     *     {
     *         "edition": "",
     *     }
     * )
     *
     * Project (subdomain) homepage
     */
    public function indexAction($subdomain)
    {
        $em = $this->getDoctrine()->getManager();

        $edition = $em->getRepository('Entity:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        return $this->forward(
            'SiteBundle:Edition:show',
            [
                'edition' => $edition->getId()
            ]
        );
    }

    /**
     * @Route(
     *     "/about",
     *     name="about",
     *     options=
     *     {
     *         "sitemap" = {
     *             "priority" = 0.7,
     *             "changefreq" = "monthly"
     *         }
     *     }
     * )
     * @Template
     * @SectionEnabled(name="about")
     */
    public function aboutAction()
    {
        $em = $this->getDoctrine()->getManager();

        $edition = $em->getRepository('Entity:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $about = $em->getRepository('Entity:About')
            ->getAll();

        return [
            'edition' => $edition,
            'about' => $about,
        ];
    }

    /**
     * @Route(
     *     "/contact",
     *     name="contact",
     *     options=
     *     {
     *         "sitemap" = true
     *     }
     * )
     * @Template
     */
    public function contactAction()
    {
        $em = $this->getDoctrine()->getManager();

        $edition = $em->getRepository('Entity:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        return [
            'edition' => $edition,
        ];
    }

    /**
     * @Route(
     *     "/rules",
     *     name="rules",
     *     options=
     *     {
     *         "sitemap" = true
     *     }
     * )
     * @Template
     * @SectionEnabled(name="rules")
     */
    public function rulesAction()
    {
        $em = $this->getDoctrine()->getManager();

        $edition = $em->getRepository('Entity:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $rules = $em->getRepository('Entity:Rule')
            ->getAll();

        return [
            'edition' => $edition,
            'rules' => $rules
        ];
    }

    /**
     * @Route(
     *     "/faq",
     *     name="faq",
     *     options=
     *     {
     *         "sitemap" = true
     *     }
     * )
     * @Template
     * @SectionEnabled(name="faq")
     */
    public function faqAction($subdomain)
    {
        $em = $this->getDoctrine()->getManager();

        $edition = $em->getRepository('Entity:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $faqs = $em->getRepository('Entity:Faq')->getAll();

        return [
            'edition' => $edition,
            'faqs' => $faqs
        ];
    }

    /**
     * @Route(
     *     "/edition/{edition}/chosen",
     *     name="edition_chosen",
     *     requirements=
     *     {
     *         "edition": "\d+",
     *     }
     * )
     * @Method("GET")
     * @Template
     * @SectionEnabled(name="mentees")
     */
    public function chosenAction(Edition $edition)
    {
        $em = $this->getDoctrine()->getManager();

        $config = $em->getRepository('Entity:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        if (!$config->getIsChosenMenteesVisible()) {
            throw $this->createNotFoundException('config.choosenMentees.not.enabled');
        }

        $persons = $em->getRepository('Entity:Person')
            ->getFromEdition($edition->getId(), true);

        // to display select options
        $editions = $em->getRepository('Entity:Edition')
            ->getBySortableGroupsQuery()->getResult();

        // find last edition
        $edition = $em->getRepository('Entity:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        return [
            'persons' => $persons,
            'edition' => $edition,
        ];
    }
}
