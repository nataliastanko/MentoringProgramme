<?php

namespace Annotation\Controller;

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
