<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup;

use Pmigut\GtinValidator\Gtin13;

final class BusinessNumberValidator
{
    public const COMPANY_PREFIX = '9429';

    public static function isValidNumber(string $businessNumber): bool
    {
        // Replace whitespace and hyphens
        $businessNumber = \preg_replace('/[\s-]+/', '', $businessNumber);
        if (null === $businessNumber) {
            return false;
        }

        // Ensure number starts with the NZBN company prefix
        if (false === \str_starts_with($businessNumber, self::COMPANY_PREFIX)) {
            return false;
        }

        return Gtin13::isValid($businessNumber);
    }
}
