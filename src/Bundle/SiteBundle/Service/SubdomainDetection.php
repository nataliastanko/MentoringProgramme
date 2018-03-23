<?php

namespace SiteBundle\Service;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class SubdomainDetection
{
    /** @var EntityManager */
    private $em;

    /** @var \Entity\Organization | null */
    private $organization;

    /** @var string */
    private $subdomain;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return \Entity\Organization | null
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * @return string | null
     */
    public function getSubdomain()
    {
        return $this->subdomain;
    }

    /**
     * @return boolean
     */
    public function isSubdomainPresent()
    {
        return $this->subdomain ? true : false;
    }

    public function detectSubdomain(GetResponseEvent $event)
    {
        $host = $event->getRequest()->getHost();

        if ($host) {
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

}
