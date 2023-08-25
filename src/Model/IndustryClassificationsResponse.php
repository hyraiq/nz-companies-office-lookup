<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup\Model;

use Symfony\Component\Serializer\Annotation\SerializedName;

final class IndustryClassificationsResponse extends AbstractResponse
{
    #[SerializedName('classificationCode')]
    public string $code;

    #[SerializedName('classificationDescription')]
    public ?string $description;
}
