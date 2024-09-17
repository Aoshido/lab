<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\DataPersister\PatientDataPersister;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/patients/{id}',
            requirements: ['id' => '\d+'],
        ),
        new Post(
            processor: PatientDataPersister::class // Explicitly set the processor for POST https://api-platform.com/docs/core/state-processors/
        ),
        new GetCollection(),
        new Put(),
        new Delete(),
        new Patch(),
    ],
    normalizationContext: [
        'groups' => ['patient:read'],
    ],
    denormalizationContext: [
        'groups' => ['patient:write'],
    ],
)]
#[ApiFilter(OrderFilter::class, properties: ['name' => 'ASC'])] // api/patients.json?order[name]=desc
class Patient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['patient:read', 'patient:write'])]
    private ?string $name = null;

    #[ORM\OneToMany(mappedBy: 'patient', targetEntity: Serum::class, cascade: ['persist', 'remove'])]
    #[Groups(['patient:read', 'patient:write'])]
    private Collection $serums;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['patient:read', 'patient:write'])]
    private ?string $dni = null;

    public function getDni(): ?string
    {
        return $this->dni;
    }

    public function setDni(string $dni): static
    {
        $this->dni = $dni;

        return $this;
    }

    public function __construct() {
        $this->serums = new ArrayCollection();
    }

    public function getSerums(): Collection
    {
        return $this->serums;
    }

    public function addSerum(Serum $serum): static
    {
        if (!$this->serums->contains($serum)) {
            $this->serums[] = $serum;
            $serum->setPatient($this);
        }

        return $this;
    }

    public function removeSerum(Serum $serum): static
    {
        if ($this->serums->removeElement($serum)) {
            // set the owning side to null (unless already changed)
            if ($serum->getPatient() === $this) {
                $serum->setPatient(null);
            }
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    // Define a new group for extraction dates
    #[Groups(['patient:read:with_dates'])]
    public function getSerumExtractionDates(): array
    {
        return $this->serums->map(function (Serum $serum) {
            return [
                'extraction_date' => $serum->getExtractionDate()->format('Y-m-d'),
            ];
        })->toArray();
    }
}
