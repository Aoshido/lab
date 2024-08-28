<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Company;
use App\Tests\Factory\CompanyFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class CompanyTest extends ApiTestCase {
    // This trait provided by Foundry will take care of refreshing the database content to a known state before each test
    use ResetDatabase, Factories;

    public function testGetCollection(): void {
        CompanyFactory::createMany(100);

        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', '/api/companies');

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/api/contexts/Company',
            '@id' => '/api/companies',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/companies?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/companies?page=1',
                'hydra:last' => '/api/companies?page=4',
                'hydra:next' => '/api/companies?page=2',
            ],
        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        $this->assertCount(30, $response->toArray()['hydra:member']);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceCollectionJsonSchema(Company::class);
    }

    public function testCreateCompany(): void {
        $response = static::createClient()->request('POST', '/api/companies', ['json' => [
            'name' => '0099740915',
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Company',
            '@type' => 'Company',
            'name' => '0099740915',
        ]);
        $this->assertMatchesRegularExpression('~^/api/companies/\d+$~', $response->toArray()['@id']);
        $this->assertMatchesResourceItemJsonSchema(Company::class);
    }

    public function testCreateInvalidCompany(): void {
        static::createClient()->request('POST', '/api/companies', ['json' => [
            'name' => '',
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');

        $this->assertJsonContains([
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => 'name: This value should not be blank.',
        ]);
    }

    public function testUpdateCompany(): void {
        CompanyFactory::createOne(['name' => '9781344037075']);

        $client = static::createClient();
        // findIriBy allows to retrieve the IRI of an item by searching for some of its properties.
        $iri = $this->findIriBy(Company::class, ['name' => '9781344037075']);

        // Use the PATCH method here to do a partial update
        $client->request('PATCH', $iri, [
            'json' => [
                'name' => 'updated name',
            ],
            'headers' => [
                'Content-Type' => 'application/merge-patch+json',
            ]
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            '@id' => $iri,
            'name' => 'updated name',
        ]);
    }

    public function testDeleteCompany(): void {
        CompanyFactory::createOne(['name' => '9781344037075']);

        $client = static::createClient();
        $iri = $this->findIriBy(Company::class, ['name' => '9781344037075']);

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
        // Through the container, you can access all your services from the Tests, including the ORM, the mailer, remote API clients...
            static::getContainer()->get('doctrine')->getRepository(Company::class)->findOneBy(['name' => '9781344037075'])
        );
    }

}