<?php

namespace App\Tests\Repository;

use App\Entity\About;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class AboutRepositoryTest extends KernelTestCase
{
    use RefreshDatabaseTrait;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine.orm.default_entity_manager');
    }

    public function testSearchByName()
    {
        $abouts = $this->entityManager
            ->getRepository(About::class)
            ->findAll()
        ;

        $about = $this->entityManager
            ->getRepository(About::class)
            ->findOneBy(['title' => 'How does it work?'])
        ;

        $this->assertCount(11, $abouts);
        $this->assertInstanceOf(About::class, $about);
    }

    protected function tearDown()
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
