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
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Annotation\Context;


#[ORM\Entity]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/serums/{id}',
            requirements: ['id' => '\d+'],
        ),
        new Post(),
        new GetCollection(),
        new Put(),
        new Delete(),
        new Patch(),
    ],
    normalizationContext: [
        'groups' => ['serum:read'],
    ],
    denormalizationContext: [
        'groups' => ['serum:write'],
    ],
)]
#[ApiFilter(OrderFilter::class, properties: ['name' => 'ASC'])] // api/serums.json?order[name]=desc
class Serum
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['serum:read', 'serum:write'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['serum:read', 'serum:write', 'patient:write'])]
    #[Context([
        DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'
    ])]
    private ?\DateTimeInterface $extractionDate = null;

    #[ORM\ManyToOne(inversedBy: 'serums')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['serum:read', 'serum:write'])]
    private ?Patient $patient = null;

    public function getExtractionDate(): ?\DateTimeInterface
    {
        return $this->extractionDate;
    }

    public function setExtractionDate(?\DateTimeInterface $extractionDate): static
    {
        $this->extractionDate = $extractionDate;

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

    public function getPatient(): ?Patient
    {
        return $this->patient;
    }

    public function setPatient(Patient $patient): static
    {
        $this->patient = $patient;

        return $this;
    }
}
