<?php

namespace Service\EventSubscriber;

use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class SubdomainAwareSubscriber implements EventSubscriberInterface
{
    /** @var Router */
    private $router;

    /** @var Session */
    private $session;

    /** @var EntityManagerInterface */
    private $em;

    /** @var \Entity\Organization | null */
    private $organization;

    /** @var string */
    private $subdomain;

    /** @var TokenStorage */
    private $userToken;

    /** @var AuthorizationChecker */
    private $authorizationChecker;

    public function __construct(Router $router, Session $session, EntityManagerInterface $em, TokenStorage $token, AuthorizationCheckerInterface $authorization)
    {
        $this->router = $router;
        $this->userToken = $token;
        $this->authorizationChecker = $authorization;
        $this->session = $session;
        $this->em = $em;
    }

    /**
     * @return array subscribed events, their methods and priorities
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['detectSubdomain', 100],
                ['setSubdomainContext', 90],
                ['checkSubdomainAccess', 30]
            ],
        ];
    }

    /**
     * Check logged user for subdomain access
     * @param  GetResponseEvent $event
     * @return void
     */
    public function checkSubdomainAccess(GetResponseEvent $event)
    {
        $request = $event->getRequest(); //$event->getKernel();

        if (null === $this->userToken->getToken()) {
            return;
        }

        // only when subdomain routing
        if ($this->isSubdomainPresent()) {

            // only when on '^/account' route
            preg_match('/account/', $request->getPathInfo(), $matches);

            // only if logged in
            if ($matches && $this->authorizationChecker->isGranted('ROLE_USER')) {
                $user = $this->userToken->getToken()->getUser();

                // check access for subdomain admin panel
                if ($user->getOrganization()->getId() !== $this->getOrganization()->getId()) {
                    throw new AccessDeniedException('Not allowed to manage given organization');
                }
            }
        }
    }

    /**
     * Detect subdomain from request
     * @param  GetResponseEvent $event
     * @return void
     */
    public function detectSubdomain(GetResponseEvent $event)
    {
        $host = $event->getRequest()->getHost();

        if ($host) {
            // Host Regex   | #^(?P<subdomain>[^\.]++)\.(?P<domain_name>[^\.]++)\.(?P<domain_ext>[^\.]++)$#sDi

            // get last 3 segments of the host
            if (preg_match("/[^\.\/]+\.[^\.\/]+\.[^\.\/]+$/", $host, $match)) {
                // $subdomain = substr($host, 0, ((strlen($this->base_host) + 1) * -1));
                $domain = explode('.', $match[0]);
                $subdomain = $domain[0];

                $this->subdomain = $subdomain;

                $organization = $this->em->getRepository('Entity:Organization')
                    ->findOneBy(
                        [
                        'subdomain' => $subdomain,
                        'isAccepted' => true
                        ]
                    );

                $this->organization = $organization;
            }
        }
    }

    /**
     * Organization from subdomain
     * @return \Entity\Organization | null
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Subdomain from request
     * @return string | null
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * If subdomain page
     * @return boolean
     */
    public function isSubdomainPresent()
    {
        return $this->subdomain ? true : false;
    }

    /**
     * Set subdomain context as routing parameter
     * @param GetResponseEvent $event
     * @return void
     */
    public function setSubdomainContext(GetResponseEvent $event)
    {
        $host = $event->getRequest()->getHost();

        $organization = $this->getOrganization();

        // no such organization and some subdomain present
        if (!$organization && $this->isSubdomainPresent()) {
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
