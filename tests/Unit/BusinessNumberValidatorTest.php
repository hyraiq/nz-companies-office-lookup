<?php

declare(strict_types=1);

namespace Hyra\Tests\NzCompaniesOfficeLookup\Unit;

use Hyra\NzCompaniesOfficeLookup\BusinessNumberValidator;
use PHPUnit\Framework\TestCase;

class BusinessNumberValidatorTest extends TestCase
{
    /**
     * @dataProvider getValidTests
     */
    public function testValidNumber(string $businessNumber): void
    {
        $result = BusinessNumberValidator::isValidNumber($businessNumber);

        static::assertTrue($result);
    }

    /**
     * @return mixed[]
     */
    public function getValidTests(): array
    {
        return [
            'no spaces'   => ['9429033128887'],
            'with dashes' => ['94-290-331-288-87'],
            'with spaces' => ['94 290 331 288 87'],
            'random 1'    => ['9429037784669'],
            'random 2'    => ['9429046593054'], // Overseas entity
        ];
    }

    /**
     * @dataProvider getInvalidTests
     */
    public function testInvalidNumber(string $businessNumber): void
    {
        $result = BusinessNumberValidator::isValidNumber($businessNumber);

        static::assertFalse($result);
    }

    /**
     * @return mixed[]
     */
    public function getInvalidTests(): array
    {
        return [
            'less than 13 characters' => ['123456789012'],
            'more than 13 characters' => ['12345678901234'],
            'invalid prefix'          => ['0231566199093'],
            'invalid GTIN-13'         => ['9429033128888'],
        ];
    }
}
