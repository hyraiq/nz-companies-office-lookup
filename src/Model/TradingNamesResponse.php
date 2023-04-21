<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup\Model;

final class TradingNamesResponse extends AbstractResponse
{
    public string $name;

    public \DateTimeImmutable $startDate;

    public ?\DateTimeImmutable $endDate = null;
}
