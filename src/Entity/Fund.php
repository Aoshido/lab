<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\GetDuplicateFunds;
use App\Repository\FundRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiFilter;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;


#[ORM\Entity(repositoryClass: FundRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/funds/{id}',
            requirements: ['id' => '\d+'],
        ),
        new Post(),
        new GetCollection(),
        new Put(),
        new Delete(),
        new Patch(),
        new Get(
            uriTemplate: '/funds/duplicates',
            formats: ['json', 'jsonld'],
            controller: GetDuplicateFunds::class,
            description: 'gets a list of possible duplicates',
            read: false,
            name: 'duplicates'
        ),
    ],
    normalizationContext: [
        'groups' => ['fund:read'],
        [DateTimeNormalizer::FORMAT_KEY => 'Y']
    ],
    denormalizationContext: [
        'groups' => ['fund:write'],
        [DateTimeNormalizer::FORMAT_KEY => 'Y']
    ],
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]            // api/funds.json?name=string
#[ApiFilter(DateFilter::class, properties: ['startYear'])]                      // api/funds.json?startYear[after]=2025-01-01
#[ApiFilter(OrderFilter::class, properties: ['name' => 'ASC'])]                 // api/funds.json?order[name]=desc
class Fund {
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['fund:read', 'fund:write', 'company:read'])]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups(['fund:read', 'fund:write', 'company:read'])]
    #[Context(
        context: [DateTimeNormalizer::FORMAT_KEY => 'Y'],
        normalizationContext: [DateTimeNormalizer::FORMAT_KEY => 'Y'],
        groups: ['fund:read', 'fund:write', 'company:read']
    )]
    #[ApiProperty(
        openapiContext: [
            'type' => 'string',
            'format' => 'date',
            'pattern' => '/([0-9]{4})/',
            'example' => '1990'
        ]
    )]
    private ?\DateTimeInterface $startYear = null;

    #[ORM\ManyToOne(targetEntity: self::class)]
    private ?self $duplicateFund = null;

    public function __construct() {
        $this->aliases = new ArrayCollection();
    }

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

    public function getStartYear(): ?\DateTimeInterface {
        return $this->startYear;
    }

    public function setStartYear(\DateTimeInterface $startYear): static {
        $this->startYear = $startYear;

        return $this;
    }

    public function getDuplicateFund(): ?self {
        return $this->duplicateFund;
    }

    public function setDuplicateFund(?self $duplicateFund): static {
        $this->duplicateFund = $duplicateFund;

        return $this;
    }


}
