<?php

namespace Annotation\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetaData;
use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\Common\Annotations\Reader;

/**
 * Dotrine 2 filter based on a relation with organization
 * @author Michaël Perrin (http://blog.michaelperrin.fr)
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class OrganizationFilter extends SQLFilter
{
    /** @var Reader */
    private $reader;

    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias)
    {
        if (empty($this->reader)) {
            return '';
        }

        // the doctrine filter is called for any query on any entity
        // check if the current entity is @OrganizationAware
        $organizationAware = $this->reader->getClassAnnotation(
            $targetEntity->getReflectionClass(),
            'Annotation\\Doctrine\\OrganizationAware'
        );

        if (!$organizationAware) {
            return '';
        }

        $fieldName = $organizationAware->organizationFieldName;

        try {
            // getParameter automatically quotes parameters
            $organizationId = $this->getParameter('id');
        } catch (\InvalidArgumentException $e) {
            return '';
        }

        if (empty($fieldName) || empty($organizationId)) {
            return '';
        }

        // INNER JOIN organization ON (org.organization_id = obj.id AND org.organization_id = 1)
        // where 1 is subdomain organization id
        $query = sprintf('%s.%s = %s', $targetTableAlias, $fieldName, $organizationId);

        return $query;
    }

    public function setAnnotationReader(Reader $reader)
    {
        $this->reader = $reader;
    }
}
