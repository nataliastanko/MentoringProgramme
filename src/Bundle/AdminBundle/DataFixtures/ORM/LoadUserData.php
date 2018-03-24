<?php

namespace AdminBundle\FataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Entity\User;

/**
 * @author Natalia Stanko <contact@nataliastanko.com>
 */
class LoadUserData implements FixtureInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        // $userAdmin = new User();
        // $userAdmin->setUsername('abc');
        // $userAdmin->setEmail('abc@def.ghi');

        // $userAdmin->setPlainPassword('123');
        // $userAdmin->addRole('ROLE_ADMIN');
        // $userAdmin->addRole('ROLE_SUPER_ADMIN');
        // $userAdmin->setEnabled(true);

        // $manager->persist($userAdmin);

        // $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 2; // the order in which fixtures will be loaded
    }
}
