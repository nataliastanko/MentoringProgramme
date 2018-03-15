<?php

namespace Wit\Program\Account\MentorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Security("is_granted('ROLE_MENTOR')")
 */
class AccountController extends Controller
{

    /**
     * @Route("/", name="account_mentor")
     * @Template
     */
    public function mentorAction()
    {
        $em = $this->getDoctrine()->getManager();

        // last edition
        $edition = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        $mentor = $this->getUser()->getInvitation()->getMentor();

        $persons = $this->getDoctrine()->getManager()
            ->getRepository('WitProgramAdminEditionBundle:Person')
            ->filterByMentorAndEdition($mentor, $edition, true); // only accepted persons

        return [
            'edition' => $edition,
            'mentor' => $mentor,
            'config' => $config,
            'persons' => $persons,
        ];
    }
}
