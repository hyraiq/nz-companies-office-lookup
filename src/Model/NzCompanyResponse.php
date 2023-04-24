<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotBlank;

final class NzCompanyResponse extends AbstractResponse
{
    #[SerializedName('entityName')]
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
     *
     * @Assert\All({
     *
     *     @Assert\Type("Hyra\NzCompaniesOfficeLookup\Model\TradingNamesResponse")
     * })
     */
    public array $tradingNames = [];

    /**
     * @var AddressResponse[]
     *
     * @Assert\All({
     *
     *     @Assert\Type("Hyra\NzCompaniesOfficeLookup\Model\AddressResponse")
     * })
     */
    public array $addresses = [];

    /**
     * @var IndustryClassificationsResponse[]
     *
     * @Assert\All({
     *
     *     @Assert\Type("Hyra\NzCompaniesOfficeLookup\Model\IndustryClassificationsResponse")
     * })
     */
    public array $industryClassifications = [];

    public function isActive(): bool
    {
        return 'registered' === \mb_strtolower($this->status);
    }
}
