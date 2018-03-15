<?php

namespace Wit\Program\Account\UserBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Doctrine\ORM\EntityManager;
use Wit\Program\Account\UserBundle\Entity\Invitation;

/**
 * Create the custom data transformer.
 * Transforms an Invitation to an invitation code.
 * @link https://symfony.com/doc/current/bundles/FOSUserBundle/adding_invitation_registration.html
 */
class InvitationToCodeTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof Invitation) {
            throw new UnexpectedTypeException($value, 'Wit\Program\Account\UserBundle\Entity\Invitation');
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
FROM WitProgramAccountUserBundle:Invitation i
WHERE i.code = :code
AND NOT EXISTS(SELECT 1 FROM WitProgramAccountUserBundle:User u WHERE u.invitation = i)
DQL;

        return $this->em
            ->createQuery($dql)
            ->setParameter('code', $value)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}
