<?php

namespace UserBundle\Controller\Mentee;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Entity\Mentor;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * Account mentor controller.
 * @Route("/mentee/mentor")
 * @Security("is_granted('ROLE_MENTEE')")
 */
class MentorController extends Controller
{
    /**
     * @Route("/{id}", name="account_mentee_mentor_show")
     * @Template
     */
    public function showAction(Mentor $mentor)
    {
        $person = $this->getUser()->getInvitation()->getPerson();

        if ($person->getMentor()->getId() !== $mentor->getId()) {
            throw $this->createAccessDeniedException('You cannot access this page');
        }

        return [
            'mentor' => $mentor,
        ];
    }
}
