<?php

namespace AdminBundle\Security\Annotation\Listener;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use AdminBundle\Security\Annotation\UsersAccounts;
use ReflectionClass;
use ReflectionObject;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UsersAccountsAnnotationListener
{
    /** @var AnnotationReader $reader */
    private $reader;

    /** @var RequestStack $requestStack */
    private $requestStack;

    /** @var EntityManager $em */
    private $em;

    /**
     * @param AnnotationReader $reader
     * @param RequestStack $stack
     */
    public function __construct(AnnotationReader $reader, RequestStack $stack, EntityManager $em)
    {
        $this->reader = $reader;
        $this->requestStack = $stack;
        $this->em = $em;
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

        $request = $this->requestStack->getCurrentRequest();

        // Override the response only if the annotation is used for method or class
        if ($this->hasUsersAccountsAnnotation($controllerObject, $methodName)) {
            $config = $this->em->getRepository('Entity:Config')->findConfig();

            if (!$config) {
                throw new NotFoundHttpException('No config found');
            }

            if (!$config->getIsUsersAccountsEnabled()) {
                throw new AccessDeniedHttpException('Users accounts disabled');
            }
        }
    }

    /**
     * @param Controller $controllerObject
     * @param string $methodName
     * @return bool
     */
    private function hasUsersAccountsAnnotation( $controllerObject, string $methodName) : bool
    {
        $tokenAnnotation = UsersAccounts::class;

        $hasAnnotation = false;

        // Get class annotation
        // Using ClassUtils::getClass in case the controller is an proxy
        $classAnnotation = $this->reader->getClassAnnotation(
            new ReflectionClass(ClassUtils::getClass($controllerObject)), $tokenAnnotation
        );

        if ($classAnnotation) {
            $hasAnnotation = true;
        }

        // Get method annotation
        $controllerReflectionObject = new ReflectionObject($controllerObject);
        $reflectionMethod = $controllerReflectionObject->getMethod($methodName);
        $methodAnnotation = $this->reader->getMethodAnnotation($reflectionMethod, $tokenAnnotation);

        if ($methodAnnotation) {
            $hasAnnotation = true;
        }

        return $hasAnnotation;
    }
}
