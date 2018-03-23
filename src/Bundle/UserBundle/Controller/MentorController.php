<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/mentor")
 * @Security("is_granted('ROLE_MENTOR')")
 */
class MentorController extends Controller
{

    /**
     * @Route("/", name="account_mentor")
     * @Template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        // last edition
        $edition = $em->getRepository('Entity:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $config = $em->getRepository('Entity:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        $mentor = $this->getUser()->getInvitation()->getMentor();

        $persons = $this->getDoctrine()->getManager()
            ->getRepository('Entity:Person')
            ->filterByMentorAndEdition($mentor, $edition, true); // only accepted persons

        return [
            'edition' => $edition,
            'mentor' => $mentor,
            'config' => $config,
            'persons' => $persons,
        ];
    }
}
