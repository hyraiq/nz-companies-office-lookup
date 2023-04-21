<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup\Stubs;

final class MockBusinessRegistryResponse
{
    /**
     * @return array<string, mixed>
     */
    public static function valid(): array
    {
        return [
            'addresses' => [
                'addressList' => [
                    [
                        'address1'    => '11 Mcdonald Street',
                        'address2'    => 'Morningside',
                        'address3'    => 'Auckland',
                        'address4'    => null,
                        'addressType' => 'REGISTERED',
                        'countryCode' => 'NZ',
                        'postCode'    => '1025',
                    ],
                    [
                        'address1'    => '11 Mcdonald Street',
                        'address2'    => 'Morningside',
                        'address3'    => 'Auckland',
                        'address4'    => null,
                        'addressType' => 'SERVICE',
                        'countryCode' => 'NZ',
                        'postCode'    => '1025',
                    ],
                ],
                'links' => [],
            ],
            'entityName'              => 'COWABUNGA BREWERIES LIMITED',
            'entityStatusDescription' => 'Registered',
            'entityTypeCode'          => 'LTD',
            'entityTypeDescription'   => 'NZ Limited Company',
            'nzbn'                    => '9429046230881',
            'registrationDate'        => '2017-07-19T16:51:53.000+1200',
            'industryClassifications' => [
                [
                    'classificationCode'        => 'C121220',
                    'classificationDescription' => 'Breweries',
                ],
            ],
            'tradingNames' => [
                [
                    'endDate'   => null,
                    'name'      => 'Cowabunga Brewing',
                    'startDate' => '2020-05-17T18:18:24.000+1200',
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function noBusinessNumberFound(): array
    {
        return [
            'errors' => [
                [
                    'error' => 'company-profile-not-found',
                    'type'  => 'ch:service',
                ],
            ],
        ];
    }
}
