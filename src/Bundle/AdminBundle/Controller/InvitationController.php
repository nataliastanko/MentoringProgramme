<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Entity\User;
use Entity\Invitation;
use Entity\Mentor;
use Entity\Person;
use AdminBundle\Security\Annotation\SectionEnabled;

/**
 * Invitation controller.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * Send invotitations to accepted people to register them to the program.
 *
 * @Route("/invite")
 * @Security("is_granted('ROLE_ADMIN')")
 * @SectionEnabled(name="mentees")
 */
class InvitationController extends Controller
{
    /**
     * @Route("/mentor/{id}", name="invite_mentor")
     * @Method("PUT")
     * @Template
     */
    public function inviteMentorAction(Mentor $mentor)
    {
        $em = $this->getDoctrine()->getManager();

        // if invitation already sent
        if ($mentor->getInvitation()) {
          throw $this->createNotFoundException('mail.invite.already.sent');
        }

        // if not assigned to any edition
        if (!count($mentor->getEditions())) {
          throw $this->createNotFoundException('Mentor can not be invited to the program');
        }

        // if signup opened
        $config = $em->getRepository('Entity:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        if ($config->getIsSignupMenteesEnabled()) {
            throw $this->createNotFoundException('Nie można wysłać zaproszenia bo zapisy są otwarte');
        }

        // create an invitation
        $invitation = new Invitation();
        $code = $invitation->getCode();
        $email = $mentor->getEmail();

        $invitation->setEmail($email);
        $invitation->setRole(Invitation::ROLE_MENTOR);

        $mentor->setInvitation($invitation);

        $registrationLink = $this->generateUrl(
          'fos_user_registration_register',
          ['code' => $code],
          UrlGeneratorInterface::ABSOLUTE_URL
        );

        $templateArgs = [
          'name' => $mentor->getFullname(),
          'registrationLink' => $registrationLink,
        ];

// return $this->render(
//   'AdminBundle:invitation/imail:invite_mentor.html.twig',
//   $templateArgs
// );

        $mailLogger = $this->get('monolog.logger.mail');

        // send mail here
        try {

        // $request = $this->get('request_stack')->getCurrentRequest();

        $senderName = $this->getParameter('email_sender_name');
        $senderAddress = $this->getParameter('email_sender_address');

        // $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        // $headerImage = $baseurl . $this->get('assets.packages')
            // ->getUrl('bundles/app/img/poster.jpg'); // with assets version

        $message = \Swift_Message::newInstance();

        // embedd an image
        // $templateArgs['headerImage'] = $message->embed(\Swift_Image::fromPath($headerImage));

        $message->setSubject(
            $this->get('translator')->trans('mail.invite.subject', [], null, 'en')
          )
          ->setFrom([$senderAddress => $senderName])
          ->setSender([$senderAddress => $senderName])
          ->setTo($email)
          ->setBody(
            $this->renderView(
              'AdminBundle:invitation/email:invite_mentor.html.twig',
              $templateArgs
            ),
            'text/html'
          )
          ;

        $transport = $this->get('swiftmailer.transport.real');
        $mailer = \Swift_Mailer::newInstance($transport);

        // Pass a variable name to the send() method
        if (!$mailer->send($message)) {
          $commaSeparated = implode(",", $failures);
          $mailLogger->critical( // content: mail.CRITICAL:
              'Problem z wysłaniem maila na adres '.$email.', '.$commaSeparated
          );
          return;
        }

        // logger success
        $mailLogger->info( // content: mail.INFO:
            'Mentor: invitation mail sent from: '.$senderAddress.' to '. $email
        );

      } catch (\Exception $e) {
        // add exception to logger
        $mailLogger->critical( // content: mail.CRITICAL:
            'Problem z wysłaniem maila na adres '.$email.', '.$e->getMessage()
        );
      }

      // mark as successfuly sent
      $invitation->send();

      $em->persist($invitation);
      $em->persist($mentor);
      $em->flush();

      // route do rejestracji musi mieć kod aktywacyjny
      return [
        'email' => $email,
      ];
    }

    /**
     * @Route("/mentee/{id}", name="invite_mentee")
     * @Template
     */
    public function inviteMenteeAction(Person $person)
    {
        $em = $this->getDoctrine()->getManager();

        // if invitation already sent
        if ($person->getInvitation()) {
          throw $this->createNotFoundException('mail.invite.already.sent');
        }

        // if not chosen
        if (!$person->getIsChosen()) {
          throw $this->createNotFoundException('Nie można wysłać zaproszenia do osoby nie wybranej do programu');
        }

        // if signup opened
        $config = $em->getRepository('Entity:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        if ($config->getIsSignupMenteesEnabled()) {
            throw $this->createNotFoundException('Nie można wysłać zaproszenia bo zapisy są otwarte');
        }

        // create an invitation
        $invitation = new Invitation();
        $code = $invitation->getCode();
        $email = $person->getEmail();

        $invitation->setEmail($email);
        $invitation->setRole(Invitation::ROLE_MENTEE);

        $person->setInvitation($invitation);

        $registrationLink = $this->generateUrl(
          'fos_user_registration_register',
          ['code' => $code],
          UrlGeneratorInterface::ABSOLUTE_URL
        );

        $templateArgs = [
          'name' => $person->getFullname(),
          'mentor' => $person->getMentor()->getFullname(),
          'registrationLink' => $registrationLink,
        ];

// return $this->render(
//   'AdminBundle:invitation/email:invite_mentee.html.twig',
//   $templateArgs
// );

        $mailLogger = $this->get('monolog.logger.mail');

        // send mail here
        try {

        // $request = $this->get('request_stack')->getCurrentRequest();

        $senderName = $this->getParameter('email_sender_name');
        $senderAddress = $this->getParameter('email_sender_address');

        // $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        // $headerImage = $baseurl . $this->get('assets.packages')
            // ->getUrl('bundles/app/img/poster.jpg'); // with assets version

        $message = \Swift_Message::newInstance();

        // embedd an image
        // $templateArgs['headerImage'] = $message->embed(\Swift_Image::fromPath($headerImage));

        $message->setSubject(
            $this->get('translator')->trans('mail.invite.subject', [], null, 'en')
          )
          ->setFrom([$senderAddress => $senderName])
          ->setSender([$senderAddress => $senderName])
          ->setTo($email)
          ->setBody(
            $this->renderView(
              'AdminBundle:invitation/email:invite_mentee.html.twig',
              $templateArgs
            ),
            'text/html'
          )
          ;

        $transport = $this->get('swiftmailer.transport.real');
        $mailer = \Swift_Mailer::newInstance($transport);

        // Pass a variable name to the send() method
        if (!$mailer->send($message)) {
          $commaSeparated = implode(",", $failures);
          $mailLogger->critical( // content: mail.CRITICAL:
              'Problem z wysłaniem maila na adres '.$email.', '.$commaSeparated
          );
          return;
        }

        // logger success
        $mailLogger->info( // content: mail.INFO:
            'Mentee: invitation mail sent from: '.$senderAddress.' to '. $email
        );

      } catch (\Exception $e) {
        // add exception to logger
        $mailLogger->critical( // content: mail.CRITICAL:
            'Problem z wysłaniem maila na adres '.$email.', '.$e->getMessage()
        );
      }

      // mark as successfuly sent
      $invitation->send();

      $em->persist($invitation);
      $em->persist($person);
      $em->flush();

      // route do rejestracji musi mieć kod aktywacyjny
      return [
        'email' => $email,
      ];
    }
}
