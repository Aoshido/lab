<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[ApiResource(
    shortName: 'Company',
    description: 'Funds are created and managed by an investment management company',
    normalizationContext: [
        'groups' => ['company:read'],
    ],
    denormalizationContext: [
        'groups' => ['company:write'],
    ]
)]
class Company {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['company:read', 'fund:read'])]
    private ?int $id = null;

    #[ORM\OneToMany(mappedBy: 'manager', targetEntity: Fund::class)]
    #[Groups(['company:read'])]
    private Collection $managedFunds;

    #[ORM\OneToMany(mappedBy: 'company', targetEntity: Fund::class)]
    #[Groups(['company:read'])]
    private Collection $assignedFunds;

    #[ORM\Column(length: 255)]
    #[Groups(['company:read', 'company:write' , 'fund:read'])]
    #[Assert\NotBlank]
    #[Assert\NotNull]
    private ?string $name = null;

    public function __construct() {
        $this->managedFunds = new ArrayCollection();
        $this->assignedFunds = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    /**
     * @return Collection<int, Fund>
     */
    public function getManagedFunds(): Collection {
        return $this->managedFunds;
    }

    public function addFund(Fund $fund): static {
        if (!$this->managedFunds->contains($fund)) {
            $this->managedFunds->add($fund);
            $fund->setCompany($this);
        }

        return $this;
    }

    public function removeFund(Fund $fund): static {
        if ($this->managedFunds->removeElement($fund)) {
            // set the owning side to null (unless already changed)
            if ($fund->getCompany() === $this) {
                $fund->setCompany(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Fund>
     */
    public function getAssignedFunds(): Collection {
        return $this->assignedFunds;
    }

    public function addAssignedFund(Fund $assignedFund): static {
        if (!$this->assignedFunds->contains($assignedFund)) {
            $this->assignedFunds->add($assignedFund);
            $assignedFund->setCompany($this);
        }

        return $this;
    }

    public function removeAssignedFund(Fund $assignedFund): static {
        if ($this->assignedFunds->removeElement($assignedFund)) {
            // set the owning side to null (unless already changed)
            if ($assignedFund->getCompany() === $this) {
                $assignedFund->setCompany(null);
            }
        }

        return $this;
    }

    public function getName(): ?string {
        return $this->name;
    }

    public function setName(string $name): static {
        $this->name = $name;

        return $this;
    }
}
