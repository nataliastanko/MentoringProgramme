<?php

namespace UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/mentee")
 * @Security("is_granted('ROLE_MENTEE')")
 */
class MenteeController extends Controller
{

    /**
     * @Route("/", name="account_mentee")
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

        $person = $this->getUser()->getInvitation()->getPerson();

        return [
            'edition' => $edition,
            'person' => $person,
        ];
    }

}
