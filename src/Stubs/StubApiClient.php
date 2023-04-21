<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup\Stubs;

use Hyra\NzCompaniesOfficeLookup\ApiClientInterface;
use Hyra\NzCompaniesOfficeLookup\BusinessNumberValidator;
use Hyra\NzCompaniesOfficeLookup\Exception\NumberInvalidException;
use Hyra\NzCompaniesOfficeLookup\Exception\NumberNotFoundException;
use Hyra\NzCompaniesOfficeLookup\Model\NzBusinessRegistryResponse;

final class StubApiClient implements ApiClientInterface
{
    /** @var array<string, NzBusinessRegistryResponse> */
    private array $companyResponses = [];

    /** @var string[] */
    private array $notFoundBusinessNumbers = [];

    public function lookupNumber(string $businessNumber): NzBusinessRegistryResponse
    {
        if (false === BusinessNumberValidator::isValidNumber($businessNumber)) {
            throw new NumberInvalidException();
        }

        if (\array_key_exists($businessNumber, $this->companyResponses)) {
            return $this->companyResponses[$businessNumber];
        }

        if (\in_array($businessNumber, $this->notFoundBusinessNumbers, true)) {
            throw new NumberNotFoundException();
        }

        throw new \LogicException(
            'Make sure you set a stub response for the business number before calling the ApiClient'
        );
    }

    public function addMockResponse(NzBusinessRegistryResponse ...$companyResponse): void
    {
        foreach ($companyResponse as $response) {
            $this->companyResponses[$response->companyNumber] = $response;
        }
    }

    public function addNotFoundBusinessNumbers(string ...$businessNumbers): void
    {
        $this->notFoundBusinessNumbers = \array_merge(
            $this->notFoundBusinessNumbers,
            $businessNumbers,
        );
    }
}
