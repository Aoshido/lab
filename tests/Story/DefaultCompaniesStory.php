<?php

namespace App\Tests\Story;

use App\Tests\Factory\CompanyFactory;
use Zenstruck\Foundry\Story;

final class DefaultCompaniesStory extends Story {
    public function build(): void {
        CompanyFactory::createMany(100);
    }
}
