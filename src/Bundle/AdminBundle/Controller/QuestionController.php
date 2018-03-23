<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Entity\Question;

/**
 * Question controller.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("/question")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class QuestionController extends Controller
{
    /**
     * Set question sortable position.
     *
     * @Route("/sort", name="question_sort")
     * @Method("POST")
     * @Template("AdminBundle:question:_list.html.twig")
     */
    public function sortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('Entity:Question');

        $questionId = $request->request->get('id');
        $position = $request->request->get('position');

        $question = $repo->findOneById($questionId);
        $question->setPosition($position);
        $em->persist($question);
        $em->flush();

        $questions = $repo->getAll();

        return [
            'questions' => $questions,
        ];
    }

    /**
     * Lists all Question entities.
     *
     * @Route("/", name="question_index")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('Entity:Question');
        $questions = $repo->getBySortableGroupsQuery()->getResult();

        return [
            'questions' => $questions,
        ];
    }

    /**
     * Creates a new Question entity.
     *
     * @Route("/new", name="question_new")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function newAction(Request $request)
    {
        $question = new Question();
        $form = $this->createForm('AdminBundle\Form\QuestionType', $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // walkaround for cascade relation
            foreach ($question->getAnswerChoices() as $choice) {
                $choice->setQuestion($question);
                $em->persist($choice);
            }

            $em->persist($question);
            $em->flush();

            return $this->redirectToRoute('question_show', array('id' => $question->getId()));
        }

        return [
            'question' => $question,
            'form' => $form->createView(),
        ];
    }

    /**
     * Finds and displays a Question entity.
     *
     * @Route("/{id}", name="question_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Question $question)
    {
        $deleteForm = $this->createDeleteForm($question);

        return [
            'question' => $question,
            'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Displays a form to edit an existing Question entity.
     *
     * @Route("/{id}/edit", name="question_edit")
     * @Method({"GET", "POST"})
     * @Template()
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function editAction(Request $request, Question $question)
    {
        // $deleteForm = $this->createDeleteForm($question);
        $editForm = $this->createForm('AdminBundle\Form\QuestionType', $question);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($question);
            $em->flush();

            return $this->redirectToRoute('question_show', array('id' => $question->getId()));
        }

        return [
            'question' => $question,
            'edit_form' => $editForm->createView(),
            // 'delete_form' => $deleteForm->createView(),
        ];
    }

    /**
     * Deletes a Question entity.
     *
     * @Route("/{id}", name="question_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function deleteAction(Request $request, Question $question)
    {
        $form = $this->createDeleteForm($question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($question);
            $em->flush();
        }

        return $this->redirectToRoute('question_index');
    }

    /**
     * Creates a form to delete a Question entity.
     *
     * @param Question $question The Question entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Question $question)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('question_delete', array('id' => $question->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
