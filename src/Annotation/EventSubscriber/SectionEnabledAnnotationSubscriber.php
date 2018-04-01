<?php

namespace Annotation\EventSubscriber;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Service\EventSubscriber\SubdomainAwareSubscriber;
use Annotation\Controller\SectionEnabled;

/**
 * Check for SectionEnabled annotation in controller
 *
 * @author delboy1978uk (https://delboy1978uk.wordpress.com/2017/10/10/creating-custom-annotations-in-symfony-3/)
 * @author Daniel Sipos (https://www.sitepoint.com/your-own-custom-annotations/)
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class SectionEnabledAnnotationSubscriber implements EventSubscriberInterface
{
    /** @var AnnotationReader $reader */
    private $reader;

    /** @var SubdomainAwareSubscriber $subdomainDetection */
    private $subdomainDetection;

    private $annotation;

    /**
     * @param AnnotationReader $reader
     * @param SubdomainAwareSubscriber $subdomainDetection
     */
    public function __construct(AnnotationReader $reader, SubdomainAwareSubscriber $subdomainDetection)
    {
        $this->reader = $reader;
        $this->subdomainDetection = $subdomainDetection;
    }

    /**
     * @return array subscribed events, their methods and priorities
     */
    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::CONTROLLER => array(
                array('onKernelController'),
            ),
        );
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

        // Get class annotation
        // Using ClassUtils::getClass in case the controller is a proxy
        $class = ClassUtils::getClass($controllerObject);
        // $class = $this->namespace . '\\' . $file->getBasename('.php');
        $classAnnotation = $this->reader->getClassAnnotation(
            new \ReflectionClass($class), $tokenAnnotation
        );

        // class has annotation
        if ($classAnnotation) {
            $hasAnnotation = true;
            $this->annotation = $classAnnotation;
        }

        // Get method annotation
        $controllerReflectionObject = new \ReflectionObject($controllerObject);
        $reflectionMethod = $controllerReflectionObject->getMethod($methodName);
        $methodAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $tokenAnnotation);

        // method has annotation
        if ($methodAnnotation) {
            $hasAnnotation = true;
            $this->annotation = $methodAnnotation;
        }

        return $hasAnnotation;
    }

    private function hasSectionAvailable(array $sectionsEnabled) : bool
    {
        return array_key_exists($this->annotation->getName(), $sectionsEnabled);
    }
}
