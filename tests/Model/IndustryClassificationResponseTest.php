<?php

declare(strict_types=1);

namespace Hyra\Tests\NzCompaniesOfficeLookup\Model;

use Hyra\NzCompaniesOfficeLookup\Model\IndustryClassificationsResponse;

final class IndustryClassificationResponseTest extends BaseModelTest
{
    public function testValidModel(): void
    {
        $data = <<<JSON
          {
            "classificationCode": "C121220",
            "classificationDescription": "Breweries",
            "uniqueIdentifier": "140987"
          }
        JSON;

        /** @var IndustryClassificationsResponse $parsed */
        $parsed = $this->valid($data, IndustryClassificationsResponse::class);

        static::assertSame('C121220', $parsed->code);
        static::assertSame('Breweries', $parsed->description);
    }

    public function testValidModelWithoutDescription(): void
    {
        $data = <<<JSON
          {
            "classificationCode": "C121220",
            "classificationDescription": null,
            "uniqueIdentifier": "140987"
          }
        JSON;

        /** @var IndustryClassificationsResponse $parsed */
        $parsed = $this->valid($data, IndustryClassificationsResponse::class);

        static::assertSame('C121220', $parsed->code);
        static::assertNull($parsed->description);
    }

    public function testInvalidModel(): void
    {
        $data = <<<JSON
          {
            "classificationCode": null,
            "classificationDescription": "Breweries",
            "uniqueIdentifier": "140987"
          }
        JSON;

        $this->invalid($data, IndustryClassificationsResponse::class);
    }
}
