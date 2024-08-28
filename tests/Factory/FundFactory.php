<?php

namespace App\Tests\Factory;

use App\Entity\Fund;
use App\Repository\FundRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Fund>
 *
 * @method        Fund|Proxy                     create(array|callable $attributes = [])
 * @method static Fund|Proxy                     createOne(array $attributes = [])
 * @method static Fund|Proxy                     find(object|array|mixed $criteria)
 * @method static Fund|Proxy                     findOrCreate(array $attributes)
 * @method static Fund|Proxy                     first(string $sortedField = 'id')
 * @method static Fund|Proxy                     last(string $sortedField = 'id')
 * @method static Fund|Proxy                     random(array $attributes = [])
 * @method static Fund|Proxy                     randomOrCreate(array $attributes = [])
 * @method static FundRepository|RepositoryProxy repository()
 * @method static Fund[]|Proxy[]                 all()
 * @method static Fund[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Fund[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Fund[]|Proxy[]                 findBy(array $attributes)
 * @method static Fund[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Fund[]|Proxy[]                 randomSet(int $number, array $attributes = [])
 */
final class FundFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function getDefaults(): array
    {
        return [
            'manager' => CompanyFactory::new(),
            'company' => CompanyFactory::new(),
            'name' => self::faker()->text(255),
            'startYear' => self::faker()->dateTime(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this
            // ->afterInstantiate(function(Fund $fund): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Fund::class;
    }
}
