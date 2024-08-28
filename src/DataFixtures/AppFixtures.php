<?php

namespace App\DataFixtures;

use App\Tests\Story\DefaultCompaniesStory;
use App\Tests\Story\DefaultFundsStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultCompaniesStory::load();
        DefaultFundsStory::load();
    }
}
