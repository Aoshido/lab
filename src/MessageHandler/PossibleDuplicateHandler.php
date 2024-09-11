<?php

namespace App\MessageHandler;

use App\Entity\Patient;
use App\Message\PossibleDuplicate;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Psr\Log\LoggerInterface;


#[AsMessageHandler]
class PossibleDuplicateHandler {

    public function __construct(private LoggerInterface $logger, private ObjectManager $objectManager) {
    }

    public function __invoke(PossibleDuplicate $message) {
        $this->logger->info($message->getContent());
        $fundRepository = $this->objectManager->getRepository(Patient::class);
        $originalFund = $fundRepository->find($message->getFundId());

        $duplicates = $fundRepository
            ->createQueryBuilder('f')
            ->where('f.name = :name')
            ->andWhere('f.manager = :manager')
            ->andWhere('f.id != :originalId')
            ->setParameters([
                'name' => $originalFund->getName(),
                'manager' => $originalFund->getManager(),
                'originalId' => $originalFund->getId(),
            ])
            ->orderBy('f.id', 'ASC')
            ->getQuery()
            ->getResult();

        // This can hugely be improved upon
        $firstDuplicate = $duplicates[0];
        $originalFund->setDuplicateFund($firstDuplicate);

        // This will also set the ID of the first duplicate to itself so we know it has duplicates
        /** @var Patient $duplicate */
        foreach ($duplicates as $duplicate){
            $duplicate->setDuplicateFund($firstDuplicate);
            $this->objectManager->persist($duplicate);
        }

        $this->objectManager->flush();
    }
}