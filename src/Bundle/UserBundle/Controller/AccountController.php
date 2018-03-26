<?php

namespace UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Entity\User;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * Account controller.
 * Welcome page after login.
 *
 * @Route("/")
 * @Security("is_granted('ROLE_USER')")
 */
class AccountController extends Controller
{
    /**
     * Forward _route issue
     * https://github.com/symfony/symfony/issues/5804
     * @Route("/", name="account")
     * @Template
     */
    public function accountAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->forward(
                'UserBundle:Account:admin',
                [
                    [],
                    '_route' => $request->attributes->get('_route'),
                    '_route_params' => $request->attributes->get('_route_params')
                ]
            );
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_MENTOR')) {
            return $this->forward(
                'UserBundle:Mentor:index',
                [
                    [],
                    '_route' => $request->attributes->get('_route'),
                    '_route_params' => $request->attributes->get('_route_params')
                ]
            );
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_MENTEE')) {
            return $this->forward(
                'UserBundle:Mentee:index',
                [
                    [],
                    '_route' => $request->attributes->get('_route'),
                    '_route_params' => $request->attributes->get('_route_params')
                ]
            );
        } else if ($this->get('security.authorization_checker')->isGranted('ROLE_TRANSLATOR')) {
            return $this->redirectToRoute('fos_user_profile_show');
        } else {
            // return new Response('Who are you?');
            // flash message
            $request->getSession()->getFlashBag()
                ->add('warning', 'Who are you?');
            return $this->redirectToRoute('fos_user_profile_show');
        }
    }

    /**
     * @Route("/admin", name="account_admin")
     * @Security("is_granted('ROLE_ADMIN')")
     * @Template
     */
    public function adminAction()
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

        // find mentors without any mentees in current edition
        $mentorsWithoutAnyMentee = $em->getRepository('Entity:Mentor')
            ->findMentorsWithoutMentees($edition->getId());
        // find mentors did not choose mentee in current edition
        $mentorsWithoutChosenMentee = $em->getRepository('Entity:Mentor')
            ->findMentorsWithoutMentees($edition->getId(), true);

        return [
            'edition' => $edition,
            'config' => $config,
            'mentorsWithoutAnyMentee' => $mentorsWithoutAnyMentee,
            'mentorsWithoutChosenMentee' => $mentorsWithoutChosenMentee,
        ];
    }

    /**
     * @Route("/{locale}", name="account_change_locale")
     * @Method("GET")
     * @TODO implement form PUT!
     */
    public function changeLocaleAction($locale, Request $request)
    {
        $user = $this->getUser();
        /**
         * @todo check if in array of available translations
         */
        $user->setLocale($locale);

        $userManager = $this->get('fos_user.user_manager');

        $userManager->updateUser($user);

        $em = $this->getDoctrine()->getManager();
        $em->flush();

        // set locale after user preference
        $this->get('session')->set('_locale', $locale);

        return $this->redirectToRoute('account');
    }

    // private function createChangeLocaleForm() {
    //     return $this->createForm(
    //         'UserBundle\Form\ChangeLocaleType',
    //         null,
    //         [
    //             'action' => $this->generateUrl('account_change_locale'),
    //             'method' => 'POST'
    //         ]
    //     );
    // }
}
