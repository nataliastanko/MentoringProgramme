<?php

namespace App\DataFixtures;

use Entity\About;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AboutFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $about = new About();
        // $about->setName("How does it work?");
        // $about->setContent("This programme is volunteer-led. It matches mentees with mentors so mentees can achieve their career goals.");
        // $manager->persist($about);

        // $about = new About();
        // $about->setName("Applications");
        // $about->setContent("Apply to join the programme.");
        // $manager->persist($about);

        // $manager->flush();
    }
}
