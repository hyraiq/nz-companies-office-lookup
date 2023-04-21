<?php

declare(strict_types=1);

namespace Hyra\Tests\NzCompaniesOfficeLookup\Integration;

use Faker\Factory;
use Faker\Generator;
use Hyra\NzCompaniesOfficeLookup\ApiClient;
use Hyra\NzCompaniesOfficeLookup\Dependencies;
use Hyra\NzCompaniesOfficeLookup\Exception\ConnectionException;
use Hyra\NzCompaniesOfficeLookup\Exception\NumberInvalidException;
use Hyra\NzCompaniesOfficeLookup\Exception\NumberNotFoundException;
use Hyra\NzCompaniesOfficeLookup\Exception\UnexpectedResponseException;
use Hyra\NzCompaniesOfficeLookup\Stubs\MockBusinessRegistryResponse;
use Hyra\NzCompaniesOfficeLookup\Stubs\StubHttpClient;
use PHPUnit\Framework\TestCase;

final class ApiClientTest extends TestCase
{
    private const BusinessNumber = '9429033128887';

    private Generator $faker;

    private ApiClient $client;

    private StubHttpClient $stubHttpClient;

    private string $apiKey;

    protected function setUp(): void
    {
        $this->faker          = Factory::create();
        $denormalizer         = Dependencies::serializer();
        $validator            = Dependencies::validator();
        $this->stubHttpClient = new StubHttpClient();
        $this->apiKey         = $this->faker->uuid;

        $this->client = new ApiClient($denormalizer, $validator, $this->stubHttpClient, $this->apiKey);
    }

    /**
     * Yes, this is a bad test. It just reimplements logic in ApiClient. However, we want to ensure the defaults
     * don't change.
     */
    public function testClientInitialisedCorrectly(): void
    {
        $this->stubHttpClient->assertDefaultOptions([
            'base_uri' => 'https://api.business.govt.nz/gateway/nzbn/v5/',
            'headers'  => [
                'Ocp-Apim-Subscription-Key' => $this->apiKey,
            ],
        ]);
    }

    public function testLookupNumberInvalidNumberDoesNotUseApi(): void
    {
        $this->expectException(NumberInvalidException::class);

        $this->client->lookupNumber($this->faker->numerify('#####'));

        $this->stubHttpClient->assertCompanyEndpointNotCalled();
    }

    public function testLookupNumberConnectionExceptionOnServerErrorResponse(): void
    {
        $this->stubHttpClient->setStubResponse([], 500);

        $this->expectException(ConnectionException::class);

        $this->client->lookupNumber(self::BusinessNumber);
    }

    public function testLookupNumberWhenNumberNotFound(): void
    {
        $this->stubHttpClient->setStubResponse(MockBusinessRegistryResponse::noBusinessNumberFound(), 404);

        $this->expectException(NumberNotFoundException::class);

        $this->client->lookupNumber(self::BusinessNumber);
    }

    public function testLookupNumberWithInvalidBusinessNumber(): void
    {
        $this->expectException(NumberInvalidException::class);

        $this->client->lookupNumber('invalid');
    }

    public function testLookupNumberHandlesUnexpectedResponse(): void
    {
        $response         = MockBusinessRegistryResponse::valid();
        $response['nzbn'] = null;
        $this->stubHttpClient->setStubResponse($response);

        $this->expectException(UnexpectedResponseException::class);

        $this->client->lookupNumber(self::BusinessNumber);

        \debug_backtrace();
    }

    public function testLookupNumberSuccess(): void
    {
        /** @var array{addresses: array{addressList: mixed[]}} $mockResponse */
        $mockResponse = MockBusinessRegistryResponse::valid();

        $this->stubHttpClient->setStubResponse($mockResponse);

        $response = $this->client->lookupNumber(self::BusinessNumber);

        $normalizedResponse = [
            'entityName'            => $response->entityName,
            'companyNumber'         => $response->companyNumber,
            'entityTypeCode'        => $response->entityTypeCode,
            'entityTypeDescription' => $response->entityTypeDescription,
            'status'                => $response->status,
            'registrationDate'      => $response->registrationDate->format('Y-m-d\TH:i:s.vO'),

            'addresses' => [
                [
                    'address1'    => $response->addresses[0]->address1,
                    'address2'    => $response->addresses[0]->address2,
                    'address3'    => $response->addresses[0]->address3,
                    'address4'    => $response->addresses[0]->address4,
                    'addressType' => $response->addresses[0]->addressType,
                    'countryCode' => $response->addresses[0]->countryCode,
                    'postcode'    => $response->addresses[0]->postCode,
                ],
                [
                    'address1'    => $response->addresses[1]->address1,
                    'address2'    => $response->addresses[1]->address2,
                    'address3'    => $response->addresses[1]->address3,
                    'address4'    => $response->addresses[1]->address4,
                    'addressType' => $response->addresses[1]->addressType,
                    'countryCode' => $response->addresses[1]->countryCode,
                    'postcode'    => $response->addresses[1]->postCode,
                ],
            ],
            'tradingNames' => [
                [
                    'name'           => $response->tradingNames[0]->name,
                    'effective_from' => $response->tradingNames[0]->startDate->format('Y-m-d\TH:i:s.vO'),
                    'ceased_on'      => $response->tradingNames[0]->endDate?->format('Y-m-d\TH:i:s.vO'),
                ],
            ],
            'industryClassifications' => [
                [
                    'classificationCode'        => $response->industryClassifications[0]->code,
                    'classificationDescription' => $response->industryClassifications[0]->description,
                ],
            ],
        ];

        $this->stubHttpClient->assertCompanyEndpointCalled([]);

        // The denormalizer pulls the addresses from the address list, so we have to do that here too
        $mockResponse['addresses'] = $mockResponse['addresses']['addressList'];

        static::assertEqualsCanonicalizing($mockResponse, $normalizedResponse);
        static::assertTrue($response->isActive());
    }
}
