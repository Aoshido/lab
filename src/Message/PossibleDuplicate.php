<?php

namespace App\Message;

class PossibleDuplicate {

    public function __construct(private string $content, private int $fundId) {
    }

    public function getContent(): string {
        return $this->content;
    }

    public function getFundId(): int {
        return $this->fundId;
    }

}