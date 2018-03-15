<?php

namespace Wit\SiteBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Wit\Program\Admin\QuestionnaireBundle\Entity\Answer;
use Wit\Program\Admin\QuestionnaireBundle\Entity\Question;
use Wit\Program\Admin\EditionBundle\Entity\Person;
use Wit\SiteBundle\Form\PersonType;

/**
 * @Route("/mentee")
 */
class MenteeController extends Controller
{
    /**
     * @Route(
     *     "/apply",
     *     name="mentee_apply"
     * )
     * @Method({"GET", "POST"})
     *
     * @Template()
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        if (!$config->getIsSignupMenteesEnabled()) {
            // flash message
            $request->getSession()->getFlashBag()
                ->add('warning', 'config.signup.closed');
            return $this->redirectToRoute('homepage');
            // throw $this->createNotFoundException('config.signup.not.enabled');
        }

        $edition = $em->getRepository('WitProgramAdminEditionBundle:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        // return $this->render('WitSiteBundle:Signup:closed.html.twig',
        //   ['edition' => $edition]
        // );

        $repo = $em->getRepository('WitProgramAdminQuestionnaireBundle:Question');
        $questions = $repo->getFromType(Question::TYPE_MENTEE);

        $person = new Person();

        $person->setEdition($edition);

        foreach ($questions as $question) {
            $answer = new Answer();
            $answer->setQuestion($question);
            $answer->setPerson($person);

            $person->getAnswers()->add($answer); // add to collection
        }

        $form = $this->createForm(
          PersonType::class,
          $person,
          ['editionId' => $edition->getId()]
        );

        $form->handleRequest($request);

        $twigArr = [
            'form' => $form->createView(),
            'edition' => $edition
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $person->setEdition($edition);

            $em->persist($person);
            $em->flush();

            $templateArgs = [
                'name' => $person->getFullname(),
                'edition' => $edition->getName()
            ];

            $this->sendEmail($person->getEmail(), $templateArgs);

            return $this->render(
                'WitSiteBundle:Mentee:thankyou.html.twig',
                $twigArr
            );
        }

        // foreach ($form->getErrors() as $key => $value) {
        //     var_dump($value->getMessage());
        // }
        // exit;

        return $twigArr;
    }

    protected function sendEmail($email, $templateArgs) {
      // send confirmation mail

      $mailLogger = $this->get('monolog.logger.mail');

      try {

        $request = $this->get('request_stack')->getCurrentRequest();

        $senderName = $this->getParameter('email_sender_name');
        $senderAddress = $this->getParameter('email_sender_address');

        // $baseurl = $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath();
        // $headerImage = $baseurl . $this->get('assets.packages')
            // ->getUrl('bundles/app/img/poster.jpg'); // with assets version

        $message = \Swift_Message::newInstance();

        // embedd an image
        // $templateArgs['headerImage'] = $message->embed(\Swift_Image::fromPath($headerImage));

        $message->setSubject($this->get('translator')->trans('mail.apply.confirmation.subject'))
          ->setFrom([$senderAddress => $senderName])
          ->setSender([$senderAddress => $senderName])
          ->setTo($email)
          ->setBody(
            $this->renderView(
              'WitSiteBundle:Mentee/Email:apply_confirmation.html.twig',
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
            'Mentee: confirmation mail sent from: '.$senderAddress.' to '. $email
        );

      } catch (\Exception $e) {
          // add exception to logger
          $mailLogger->critical( // content: mail.CRITICAL:
              'Problem z wysłaniem maila na adres '.$email.', '.$e->getMessage()
          );
      }

    }
}
