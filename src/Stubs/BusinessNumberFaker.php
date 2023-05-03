<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup\Stubs;

use Hyra\NzCompaniesOfficeLookup\BusinessNumberValidator;

final class BusinessNumberFaker
{
    public static function validBusinessNumber(): string
    {
        // NZBN company prefix
        $companyPrefix = BusinessNumberValidator::COMPANY_PREFIX;

        // @phpstan-ignore-next-line it can find an appropriate source of randomness
        $randomNumber = \str_pad((string) \random_int(0, 99_999_999), 8, '0', \STR_PAD_LEFT);

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
        $randomNumber = \str_pad((string) \random_int(0, 999_999_999), 9, '0', \STR_PAD_LEFT);

        return \sprintf('1234%s', $randomNumber);
    }
}
