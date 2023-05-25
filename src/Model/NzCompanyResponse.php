<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

final class NzCompanyResponse extends AbstractResponse
{
    #[NotBlank]
    public string $entityName;

    #[SerializedName('nzbn')]
    #[NotBlank]
    public string $companyNumber;

    #[NotBlank]
    public string $entityTypeCode;

    #[NotBlank]
    public string $entityTypeDescription;

    #[SerializedName('entityStatusDescription')]
    #[NotBlank]
    public string $status;

    #[NotBlank]
    public \DateTimeImmutable $registrationDate;

    /**
     * @var TradingNamesResponse[]
     */
    #[Assert\All(constraints: new Assert\Type(TradingNamesResponse::class))]
    public array $tradingNames = [];

    /**
     * @var AddressResponse[]
     */
    #[Assert\All(constraints: new Assert\Type(AddressResponse::class))]
    public array $addresses = [];

    /**
     * @var IndustryClassificationsResponse[]
     */
    #[Assert\All(constraints: new Assert\Type(IndustryClassificationsResponse::class))]
    public array $industryClassifications = [];

    public function isActive(): bool
    {
        return 'registered' === \mb_strtolower($this->status);
    }
}
