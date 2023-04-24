<?php

declare(strict_types=1);

namespace Hyra\Tests\NzCompaniesOfficeLookup\Model;

use Hyra\NzCompaniesOfficeLookup\Model\AddressResponse;
use Hyra\NzCompaniesOfficeLookup\Model\NzCompanyResponse;

final class NzCompanyResponseTest extends BaseModelTest
{
    public function testValidModel(): void
    {
        $data = $this->getValidResponse();

        $parsed = $this->valid($data, NzCompanyResponse::class);

        static::assertSame('9429046230881', $parsed->companyNumber);
        static::assertSame('COWABUNGA BREWERIES LIMITED', $parsed->entityName);
        static::assertSame('LTD', $parsed->entityTypeCode);
        static::assertSame('NZ Limited Company', $parsed->entityTypeDescription);
        static::assertSame('Registered', $parsed->status);
        static::assertSame('2017-07-19', $parsed->registrationDate->format('Y-m-d'));
        static::assertCount(1, $parsed->tradingNames);
        static::assertCount(1, $parsed->industryClassifications);
        static::assertCount(2, $parsed->addresses);

        /** @var AddressResponse[] $addresses */
        $addresses = $parsed->addresses;

        // First address
        static::assertSame('11 Mcdonald Street', $addresses[0]->address1);
        static::assertSame('Morningside', $addresses[0]->address2);
        static::assertSame('Auckland', $addresses[0]->address3);
        static::assertNull($addresses[0]->address4);
        static::assertSame('REGISTERED', $addresses[0]->addressType);
        static::assertSame('NZ', $addresses[0]->countryCode);
        static::assertSame('1025', $addresses[0]->postCode);
        // Second address
        static::assertSame('11 Mcdonald Street', $addresses[1]->address1);
        static::assertSame('Morningside', $addresses[1]->address2);
        static::assertSame('Auckland', $addresses[1]->address3);
        static::assertNull($addresses[1]->address4);
        static::assertSame('SERVICE', $addresses[1]->addressType);
        static::assertSame('NZ', $addresses[1]->countryCode);
        static::assertSame('1025', $addresses[1]->postCode);
    }

    /**
     * @dataProvider getInValidTests
     *
     * @param string[] $keys
     */
    public function testInvalidModel(array $keys): void
    {
        $data = $this->getValidResponse();

        foreach ($keys as $key) {
            $data = $this->removeProperty($data, $key);
        }

        $this->invalid($data, NzCompanyResponse::class);
    }

    /**
     * @return array<array-key, mixed>
     */
    public function getInValidTests(): array
    {
        return [
            'missing entityName'              => [['entityName']],
            'missing nzbn'                    => [['nzbn']],
            'missing entityTypeCode'          => [['entityTypeCode']],
            'missing entityTypeDescription'   => [['entityTypeDescription']],
            'missing entityStatusDescription' => [['entityStatusDescription']],
            'missing registrationDate'        => [['registrationDate']],
        ];
    }

    private function removeProperty(string $data, string $key): string
    {
        /** @var mixed[] $decoded */
        $decoded = \json_decode($data, true);

        unset($decoded[$key]);

        return \json_encode($decoded, \JSON_THROW_ON_ERROR);
    }

    private function getValidResponse(): string
    {
        return <<<JSON
        {
            "addresses": {
                "addressList": [
                    {
                        "address1": "11 Mcdonald Street",
                        "address2": "Morningside",
                        "address3": "Auckland",
                        "address4": null,
                        "addressType": "REGISTERED",
                        "careOf": null,
                        "countryCode": "NZ",
                        "endDate": null,
                        "pafId": null,
                        "postCode": "1025",
                        "startDate": "2021-01-26T00:00:00.000+1300",
                        "uniqueIdentifier": "94295679"
                    },
                    {
                        "address1": "11 Mcdonald Street",
                        "address2": "Morningside",
                        "address3": "Auckland",
                        "address4": null,
                        "addressType": "SERVICE",
                        "careOf": null,
                        "countryCode": "NZ",
                        "endDate": null,
                        "pafId": null,
                        "postCode": "1025",
                        "startDate": "2021-01-26T00:00:00.000+1300",
                        "uniqueIdentifier": "94295658"
                    }
                ],
                "links": []
            },
            "australianBusinessNumber": "",
            "businessEthnicityIdentifiers": [],
            "emailAddresses": [
                {
                    "emailAddress": "stuart@cowabungabrewing.co.nz",
                    "emailPurpose": "Primary",
                    "emailPurposeDescription": "Primary",
                    "startDate": "2021-01-18T16:27:13.000+1300",
                    "uniqueIdentifier": "94295657"
                }
            ],
            "entityName": "COWABUNGA BREWERIES LIMITED",
            "entityStatusCode": "50",
            "entityStatusDescription": "Registered",
            "entityTypeCode": "LTD",
            "entityTypeDescription": "NZ Limited Company",
            "gstNumbers": [
                {
                    "gstNumber": "123392094",
                    "purpose": null,
                    "startDate": "2020-05-27T14:42:57.000+1200",
                    "uniqueIdentifier": "723221"
                }
            ],
            "hibernationStatusCode": null,
            "hibernationStatusDescription": null,
            "industryClassifications": [
                {
                    "classificationCode": "C121220",
                    "classificationDescription": "Breweries",
                    "uniqueIdentifier": "140987"
                }
            ],
            "lastUpdatedDate": "2022-10-04T13:40:51.000+1300",
            "non-company-details": null,
            "nzbn": "9429046230881",
            "phoneNumbers": [
                {
                    "phoneAreaCode": "27",
                    "phoneCountryCode": "64",
                    "phoneNumber": "5320178",
                    "phonePurpose": null,
                    "phonePurposeDescription": null,
                    "startDate": "2018-10-03T14:36:40.000+1300",
                    "uniqueIdentifier": "54328299"
                }
            ],
            "registrationDate": "2017-07-19T16:51:53.000+1200",
            "sourceRegister": "COMPANY",
            "sourceRegisterUniqueIdentifier": "6324367",
            "supporting-information": null,
            "tradingNames": [
                {
                    "endDate": null,
                    "name": "Cowabunga Brewing",
                    "startDate": "2020-05-17T18:18:24.000+1200",
                    "uniqueIdentifier": "847142"
                }
            ],
            "websites": []
        }
        JSON;
    }
}
