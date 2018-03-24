<?php

namespace AdminBundle\Security\Annotation\Listener;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use SiteBundle\Service\SubdomainDetection;
use AdminBundle\Security\Annotation\SectionEnabled;

/**
 * @author delboy1978uk (https://delboy1978uk.wordpress.com/2017/10/10/creating-custom-annotations-in-symfony-3/)
 * @author Daniel Sipos (https://www.sitepoint.com/your-own-custom-annotations/)
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class SectionEnabledAnnotationListener
{
    /** @var AnnotationReader $reader */
    private $reader;

    /** @var SubdomainDetection $subdomainDetection */
    private $subdomainDetection;

    private $classAnnotation;

    /**
     * @param AnnotationReader $reader
     * @param SubdomainDetection $subdomainDetection
     */
    public function __construct(AnnotationReader $reader, SubdomainDetection $subdomainDetection)
    {
        $this->reader = $reader;
        $this->subdomainDetection = $subdomainDetection;
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onKernelController(FilterControllerEvent $event)
    {
        $controller = $event->getController();
        /*
         * $controller passed can be either a class or a Closure.
         * This is not usual in Symfony2 but it may happen.
         * If it is a class, it comes in array format
         *
         */
        if (!is_array($controller)) {
            return;
        }

        /** @var Controller $controllerObject */
        list($controllerObject, $methodName) = $controller;

        // Override the response only if the annotation is used for method or class
        if ($this->hasSectionEnabledAnnotation($controllerObject, $methodName)) {

            $organization = $this->subdomainDetection->getOrganization();

            if (!$organization) {
                throw new NotFoundHttpException('No organization found');
            }

            $sectionsEnabled = $organization->getSectionsEnabledArray();

            if (!$this->hasSectionAvailable($sectionsEnabled)) {
                throw new AccessDeniedHttpException('Section disabled');

            }
        }
    }

    /**
     * @param Controller $controllerObject
     * @param string $methodName
     * @return bool
     */
    private function hasSectionEnabledAnnotation($controllerObject, string $methodName) : bool
    {
        $tokenAnnotation = SectionEnabled::class;

        $hasAnnotation = false;

        // $class = $this->namespace . '\\' . $file->getBasename('.php');
        $class = ClassUtils::getClass($controllerObject);

        // Get class annotation
        // Using ClassUtils::getClass in case the controller is an proxy
        $this->classAnnotation = $this->reader->getClassAnnotation(
            new \ReflectionClass($class), $tokenAnnotation
        );

        // class has annotation
        if ($this->classAnnotation) {
            $hasAnnotation = true;
        }

        // Get method annotation
        $controllerReflectionObject = new \ReflectionObject($controllerObject);
        $reflectionMethod = $controllerReflectionObject->getMethod($methodName);
        $methodAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $tokenAnnotation);

        // method has annotation
        if ($methodAnnotation) {
            $hasAnnotation = true;
        }

        return $hasAnnotation;
    }

    private function hasSectionAvailable(array $sectionsEnabled) : bool
    {
        return array_key_exists($this->classAnnotation->getName(), $sectionsEnabled);
    }
}
