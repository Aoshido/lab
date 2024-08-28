<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\AliasRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AliasRepository::class)]
#[ApiResource(
    shortName: 'Alias',
    description: 'Other names for funds',
    normalizationContext: [
        'groups' => ['alias:read'],
    ],
    denormalizationContext: [
        'groups' => ['alias:write'],
    ]
)]
class Alias {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['alias:read', 'alias:write', 'fund:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['alias:read', 'alias:write', 'fund:read' , 'fund:write'])]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'aliases')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['alias:read', 'alias:write'])]
    private ?Fund $fund = null;

    public function getId(): ?int {
        return $this->id;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;

        return $this;
    }

    public function getFund(): ?Fund {
        return $this->fund;
    }

    public function setFund(?Fund $fund): static {
        $this->fund = $fund;

        return $this;
    }
}
