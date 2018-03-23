<?php

namespace SiteBundle\Service\Routing;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use SiteBundle\Service\SubdomainDetection;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class SubdomainAwareRouting
{
    /** @var Router */
    private $router;

    /** @var SubdomainDetection */
    private $subdomainDetection;

    /** @var Session */
    private $session;

    public function __construct(Router $router, Session $session, SubdomainDetection $subdomainDetection)
    {
        $this->router = $router;
        $this->session = $session;
        $this->subdomainDetection = $subdomainDetection;
    }

    public function onRequest(GetResponseEvent $event)
    {
        $host = $event->getRequest()->getHost();

        $organization = $this->subdomainDetection->getOrganization();

        // no such organization and some subdomain present
        if (!$organization && $this->subdomainDetection->isSubdomainPresent()) {
            $url = $this->router->generate('landingpage');

            $response = new RedirectResponse($url);

            // Add flash message
            $this->session->getFlashBag()
                ->add('warning', 'Invalid subdomain');

            $event->setResponse($response);
            // $event->setResponse(new Response('Organization not found', 403));
            return;
        }

        if ($organization) {
            $subdomain = $organization->getSubdomain();

            // remember subdomain
            $event->getRequest()->attributes->set('subdomain', $subdomain);
            // remember subdomain param for default routing
            $context = $this->router->getContext();
            if (!$context->hasParameter('subdomain')) {
                $context->setParameter('subdomain', $subdomain);
                $context = $this->router->getContext();
            }
        }
    }

}
