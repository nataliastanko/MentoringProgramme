<?php

namespace AdminBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Entity\Config;

/**
 * Config controller.
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 *
 * @Route("config")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class ConfigController extends Controller
{
    /**
     * Show the current config.
     *
     * @Route("/", name="config_index")
     * @Method("GET")
     * @Template
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $config = $em->getRepository('Entity:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        $edition = $em->getRepository('Entity:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        return [
            'config' => $config,
            'edition' => $edition
        ];
    }

    /**
     * Displays a form to edit an existing config entity.
     *
     * @Route("/{id}/edit", name="config_edit")
     * @Method({"GET", "POST"})
     * @Template
     */
    public function editAction(Request $request, Config $config)
    {
        $editForm = $this->createForm('AdminBundle\Form\ConfigType', $config);
        $editForm->handleRequest($request);

        $edition = $this->getDoctrine()->getRepository('Entity:Edition')
            ->findLastEdition();

        if (!$edition) {
            throw $this->createNotFoundException('No edition found');
        }

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('config_index');
        }

        return [
            'config' => $config,
            'edit_form' => $editForm->createView(),
            'edition' => $edition
        ];
    }

}
