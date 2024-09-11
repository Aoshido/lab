<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Company;
use App\Entity\Patient;
use App\Tests\Factory\CompanyFactory;
use App\Tests\Factory\FundFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class FundTest extends ApiTestCase {
    // This trait provided by Foundry will take care of refreshing the database content to a known state before each test
    use ResetDatabase, Factories;

    public function testGetCollection(): void {
        FundFactory::createMany(100);

        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', '/api/funds');

        $this->assertResponseIsSuccessful();
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            '@context' => '/api/contexts/Patient',
            '@id' => '/api/funds',
            '@type' => 'hydra:Collection',
            'hydra:totalItems' => 100,
            'hydra:view' => [
                '@id' => '/api/funds?page=1',
                '@type' => 'hydra:PartialCollectionView',
                'hydra:first' => '/api/funds?page=1',
                'hydra:last' => '/api/funds?page=4',
                'hydra:next' => '/api/funds?page=2',
            ],
        ]);

        // Because test fixtures are automatically loaded between each test, you can assert on them
        $this->assertCount(30, $response->toArray()['hydra:member']);
    }

    public function testCreateFund(): void {
        CompanyFactory::createOne(['name' => '9781344037075']);

        $client = static::createClient();
        // findIriBy allows to retrieve the IRI of an item by searching for some of its properties.
        $companyIri = $this->findIriBy(Company::class, ['name' => '9781344037075']);

        $response = static::createClient()->request('POST', '/api/funds', ['json' => [
            'name' => '0099740915',
            'startYear' => "1990",
            'manager' => $companyIri,
            'company' => $companyIri,
        ]]);

        $this->assertResponseStatusCodeSame(201);
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains([
            '@context' => '/api/contexts/Patient',
            '@type' => 'Patient',
            'name' => '0099740915',
        ]);
        $this->assertMatchesRegularExpression('~^/api/funds/\d+$~', $response->toArray()['@id']);
    }

    public function testCreateInvalidFund(): void {
        static::createClient()->request('POST', '/api/funds', ['json' => [
            'name' => 'string',
            'startYear' => '1990',
        ]]);

        $this->assertResponseStatusCodeSame(422);
        $this->assertResponseHeaderSame('content-type', 'application/problem+json; charset=utf-8');

        $this->assertJsonContains([
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'violations' => [
                [
                    'propertyPath' => 'manager',
                    'message' => 'This value should not be blank.',
                ],
                [
                    'propertyPath' => 'manager',
                    'message' => 'This value should not be null.',
                ],
                [
                    'propertyPath' => 'company',
                    'message' => 'This value should not be blank.',
                ],
                [
                    'propertyPath' => 'company',
                    'message' => 'This value should not be null.',
                ]

            ]
        ]);
    }


    public function testUpdateFund(): void {
        FundFactory::createOne(['name' => '9781344037075']);

        $client = static::createClient();
        // findIriBy allows to retrieve the IRI of an item by searching for some of its properties.
        $iri = $this->findIriBy(Patient::class, ['name' => '9781344037075']);

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

    public function testDeleteFund(): void {
        FundFactory::createOne(['name' => '9781344037075']);

        $client = static::createClient();
        $iri = $this->findIriBy(Patient::class, ['name' => '9781344037075']);

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(204);
        $this->assertNull(
        // Through the container, you can access all your services from the Tests, including the ORM, the mailer, remote API clients...
            static::getContainer()->get('doctrine')->getRepository(Patient::class)->findOneBy(['name' => '9781344037075'])
        );
    }

    public function testDuplicateWarning(): void {
        FundFactory::createOne(['name' => '9781344037075']);

        /** @var Patient $fund */
        $fund = static::getContainer()->get('doctrine')->getRepository(Patient::class)->findOneBy(['name' => '9781344037075']);
        $companyIri = $this->findIriBy(Company::class, ['id' => $fund->getManager()]);

        $response = static::createClient()->request('POST', '/api/funds', ['json' => [
            'name' => $fund->getName(),
            'startYear' => "1990",
            'manager' => $companyIri,
            'company' => $companyIri,
        ]]);

        $this->assertStringContainsString("Possible duplicate found.", static::getContainer()->get('messenger.default_bus')->getDispatchedMessages()[0]["message"]->getContent());
    }

}