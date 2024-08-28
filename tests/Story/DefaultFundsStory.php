<?php

namespace App\Tests\Story;

use App\Tests\Factory\FundFactory;
use Zenstruck\Foundry\Story;

final class DefaultFundsStory extends Story {
    public function build(): void {
        FundFactory::createMany(100);
    }
}
