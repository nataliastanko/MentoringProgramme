<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Entity\Answer;
use Annotation\Controller\SectionEnabled;

/**
 * Answer controller.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/answer")
 * @Security("is_granted('ROLE_ADMIN')")
 * @SectionEnabled(name="mentees")
 */
class AnswerController extends Controller
{
    /**
     * Lists all Answer entities.
     *
     * @Route(
     * "/question/{id}/{page}",
     * name="answer_index",
     * defaults={"page":1},
     * requirements={"page":"\d+"}
     * )
     * @Method("GET")
     * @Template()
     */
    public function indexAction($id, $page, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $question = $em->getRepository('Entity:Question')->findOneById($id);

        $query = $em
            ->createQuery('SELECT a FROM AdminBundle:Answer a WHERE a.question = :id')
            ->setParameter(':id', $id);

        $answers = $this->get('knp_paginator')->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1) /*page number*/,
            $this->get('service_container')->getParameter('paginate.limit') /*limit per page*/
        );

        return [
            'answers' => $answers,
            'question' => $question,
        ];
    }

    /**
     * Finds and displays a Answer entity.
     *
     * @Route("/{id}", name="answer_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Answer $answer)
    {
        return [
            'answer' => $answer,
        ];
    }
}
