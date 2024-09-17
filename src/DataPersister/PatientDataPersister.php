<?php

// src/DataPersister/PatientDataPersister.php

namespace App\DataPersister;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Patient;
use Doctrine\ORM\EntityManagerInterface;

class PatientDataPersister implements ProcessorInterface
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Patient
    {
        // Check if patient with the same DNI exists
        $existingPatient = $this->entityManager->getRepository(Patient::class)
            ->findOneBy(['dni' => $data->getDni()]);

        if ($existingPatient) {
            // Add the new serums to the existing patient
            foreach ($data->getSerums() as $newSerum) {
                $existingPatient->addSerum($newSerum);
            }
            // Don't persist the patient again, just flush
            $this->entityManager->flush();

            return $existingPatient;
        }

        // If it's a new patient, persist as usual
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}



