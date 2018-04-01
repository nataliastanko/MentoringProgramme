<?php

namespace UserBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\ORM\EntityManagerInterface;
use Entity\Invitation;

/**
 * Create the custom data transformer.
 * Transforms an Invitation to an invitation code.
 * @link https://symfony.com/doc/current/bundles/FOSUserBundle/adding_invitation_registration.html
 *
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class InvitationToCodeTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Invitation) {
            throw new UnexpectedTypeException($value, 'Entity\Invitation');
        }

        return $value->getCode();
    }

    public function reverseTransform($value)
    {
        if (null === $value || '' === $value) {
            return null;
        }

        if (!is_string($value)) {
            throw new UnexpectedTypeException($value, 'string');
        }

        // znajdź zaproszenie po kodzie zaproszenia,
        // które nie ma jeszcze połączenia z userem
        $dql = <<<DQL
SELECT i
FROM Entity:Invitation i
WHERE i.code = :code
AND NOT EXISTS(SELECT 1 FROM Entity:User u WHERE u.invitation = i)
DQL;

        return $this->em
            ->createQuery($dql)
            ->setParameter('code', $value)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}
