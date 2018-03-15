<?php

namespace Wit\Program\Account\MenteeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Wit\Program\Admin\EditionBundle\Entity\Mentor;

/**
 * Account mentor controller.
 * @Route("/mentor")
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
