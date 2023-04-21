<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup;

use Hyra\NzCompaniesOfficeLookup\Exception\ConnectionException;
use Hyra\NzCompaniesOfficeLookup\Exception\NumberInvalidException;
use Hyra\NzCompaniesOfficeLookup\Exception\NumberNotFoundException;
use Hyra\NzCompaniesOfficeLookup\Model\NzBusinessRegistryResponse;

interface ApiClientInterface
{
    /**
     * @throws NumberInvalidException
     * @throws ConnectionException
     * @throws NumberNotFoundException
     */
    public function lookupNumber(string $businessNumber): NzBusinessRegistryResponse;
}
