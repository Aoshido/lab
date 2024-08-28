<?php

namespace App\DataFixtures;

use App\Tests\Story\DefaultFundsStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultFundsStory::load();
    }
}
