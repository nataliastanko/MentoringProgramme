<?php

namespace Wit\Program\Admin\EditionBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Wit\Program\Admin\EditionBundle\Entity\Config;

/**
 * Config controller.
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

        $config = $em->getRepository('WitProgramAdminEditionBundle:Config')->findConfig();

        if (!$config) {
            throw $this->createNotFoundException('No config found');
        }

        $edition = $em->getRepository('WitProgramAdminEditionBundle:Edition')
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
        $editForm = $this->createForm('Wit\Program\Admin\EditionBundle\Form\ConfigType', $config);
        $editForm->handleRequest($request);

        $edition = $this->getDoctrine()->getRepository('WitProgramAdminEditionBundle:Edition')
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
