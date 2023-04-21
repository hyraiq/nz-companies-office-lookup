<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup\Model;

final class AddressResponse extends AbstractResponse
{
    public ?string $address1 = null;

    public ?string $address2 = null;

    public ?string $address3 = null;

    public ?string $address4 = null;

    public ?string $addressType = null;

    public ?string $countryCode = null;

    public ?string $postCode = null;
}
