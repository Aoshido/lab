<?php

namespace App\Tests\Factory;

use App\Entity\Patient;
use App\Repository\FundRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Patient>
 *
 * @method        Patient|Proxy                     create(array|callable $attributes = [])
 * @method static Patient|Proxy                     createOne(array $attributes = [])
 * @method static Patient|Proxy                     find(object|array|mixed $criteria)
 * @method static Patient|Proxy                     findOrCreate(array $attributes)
 * @method static Patient|Proxy                     first(string $sortedField = 'id')
 * @method static Patient|Proxy                     last(string $sortedField = 'id')
 * @method static Patient|Proxy                     random(array $attributes = [])
 * @method static Patient|Proxy                     randomOrCreate(array $attributes = [])
 * @method static FundRepository|RepositoryProxy repository()
 * @method static Patient[]|Proxy[]                 all()
 * @method static Patient[]|Proxy[]                 createMany(int $number, array|callable $attributes = [])
 * @method static Patient[]|Proxy[]                 createSequence(iterable|callable $sequence)
 * @method static Patient[]|Proxy[]                 findBy(array $attributes)
 * @method static Patient[]|Proxy[]                 randomRange(int $min, int $max, array $attributes = [])
 * @method static Patient[]|Proxy[]                 randomSet(int $number, array $attributes = [])
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
            // ->afterInstantiate(function(Patient $fund): void {})
        ;
    }

    protected static function getClass(): string
    {
        return Patient::class;
    }
}
