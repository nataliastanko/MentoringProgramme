<?php

namespace Repository\Annotation;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target("CLASS")
 * @author Michaël Perrin (http://blog.michaelperrin.fr)
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
final class OrganizationAware
{
    public $organizationFieldName;
}
