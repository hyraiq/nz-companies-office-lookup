<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup\Stubs;

final class BusinessNumberFaker
{
    private function __construct()
    {
    }

    public static function validBusinessNumber(): string
    {
        // NZBN company prefix
        $companyPrefix = '9429';

        // @phpstan-ignore-next-line it can find an appropriate source of randomness
        $randomNumber = \str_pad((string) \random_int(0, 9999999), 8, '0', \STR_PAD_LEFT);

        $checkDigit = 0;
        for ($i = 0; $i < 12; $i += 2) {
            $checkDigit += (int) \mb_substr($companyPrefix . $randomNumber, $i, 1);
            $checkDigit += (int) \mb_substr($companyPrefix . $randomNumber, $i + 1, 1) * 3;
        }
        $checkDigit = (10 - ($checkDigit % 10)) % 10;

        return $companyPrefix . $randomNumber . (string) $checkDigit;
    }

    public static function invalidBusinessNumber(): string
    {
        // @phpstan-ignore-next-line it can find an appropriate source of randomness
        return \str_pad((string) \random_int(0, 9999999), 13, '0', \STR_PAD_LEFT);
    }
}
