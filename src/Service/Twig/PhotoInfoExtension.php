<?php

namespace Service\Twig;

class PhotoInfoExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('dimension', array($this, 'dimensionFilter')),
        );
    }

    public function dimensionFilter($relativeSrc)
    {
        list($width, $height) = getimagesize($relativeSrc);
        return [
            'height' => $height,
            'width' => $width
        ];
    }
}
