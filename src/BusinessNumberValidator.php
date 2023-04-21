<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup;

use Pmigut\GtinValidator\Gtin13;

final class BusinessNumberValidator
{
    public static function isValidNumber(string $businessNumber): bool
    {
        // Replace whitespace and hyphens
        $businessNumber = \preg_replace('/[\s-]+/', '', $businessNumber);
        if (null === $businessNumber) {
            return false;
        }

        return Gtin13::isValid($businessNumber);
    }
}
