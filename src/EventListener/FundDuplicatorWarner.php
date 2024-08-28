<?php

// src/EventListener/UserChangedNotifier.php
namespace App\EventListener;

use App\Entity\Fund;
use App\Message\PossibleDuplicate;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsEntityListener(event: Events::postPersist, method: 'postPersist', entity: Fund::class)]
class FundDuplicatorWarner {

    public function __construct(private MessageBusInterface $bus) {
    }

    public function postPersist(Fund $fund, PostPersistEventArgs $args): void {
        // Just check for ONE duplicate, if there are more the consumer will handle it
        $duplicate = $args->getObjectManager()
            ->getRepository(Fund::class)
            ->findOneBy([
                'name' => $fund->getName()
            ]);

        if (!is_null($duplicate)) {
            $this->bus->dispatch(new PossibleDuplicate(
                'Possible duplicate found. NewFundId : [' . $fund->getId() . ']'
                . ' DuplicateFundId: [' . $duplicate->getId() . ']', $fund->getId()));
        }

    }
}