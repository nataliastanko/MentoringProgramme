<?php

namespace AdminBundle\Security\Annotation;

/**
 * @Annotation
 */
class SectionEnabled
{
    /**
     * @Required
     *
     * @var string
     */
    public $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
