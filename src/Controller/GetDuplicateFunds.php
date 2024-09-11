<?php

namespace App\Controller;

use App\Entity\Patient;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetDuplicateFunds extends AbstractController {

    public function __construct(private ObjectManager $objectManager) {

    }

    public function __invoke(): array {
        $fundsRepository = $this->objectManager->getRepository(Patient::class);
        $fundsWithDuplicates = $fundsRepository->createQueryBuilder('f')
            ->select('MIN(f.id)')
            ->where('f.duplicateFund IS NOT NULL')
            ->addGroupBy('f.duplicateFund')
            ->getQuery()
            ->getResult();

        $response = [];

        foreach ($fundsWithDuplicates as $duplicate) {
            $amount = $fundsRepository->createQueryBuilder('f')
                ->select('COUNT(f.id)')
                ->where('f.duplicateFund = :id')
                ->setParameter(':id', $duplicate)
                ->addGroupBy('f.duplicateFund')
                ->getQuery()
                ->getSingleScalarResult();

            $response[] = [
                "duplicateFundId" => $duplicate["1"],
                "duplicateAmount" => $amount
            ];
        }

        return $response;
    }
}