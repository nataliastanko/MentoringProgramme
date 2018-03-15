<?php

namespace Wit\Program\Account\MenteeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Security("is_granted('ROLE_MENTEE')")
 */
class AccountController extends Controller
{

    /**
     * @Route("/", name="account_mentee")
     * @Template
     */
    public function menteeAction()
    {
        $em = $this->getDoctrine()->getManager();

        // last edition
        $edition = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        $person = $this->getUser()->getInvitation()->getPerson();

        return [
            'edition' => $edition,
            'person' => $person,
        ];
    }

}
