<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup\Model;

use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @psalm-suppress UnusedClass
 */
final class AddressDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    /**
     * @param mixed $data
     *
     * @psalm-assert-if-true array{addressList:array, links:array} $data
     */
    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return 'Hyra\NzCompaniesOfficeLookup\Model\AddressResponse[]' === $type && \is_array($data) && isset($data['addressList']);
    }

    /**
     * {@inheritdoc}
     *
     * @param mixed   $data    Object to normalize
     * @param mixed[] $context Context options for the normalizer
     */
    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        if (false === $this->supportsDenormalization($data, $type, $format)) {
            // This will never happen, because Symfony calls `supportsDenormalization` before calling `denormalize`.
            // But calling the function here tells psalm the type of $data because of the @psalm-assert-if-true.
            throw new \LogicException(\sprintf('Denormalize called with unsupported type: %s', $type));
        }

        return $this->denormalizer->denormalize($data['addressList'], $type);
    }

    /**
     * @return array<'*'|'object'|class-string|string, null|bool>
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            'Hyra\NzCompaniesOfficeLookup\Model\AddressResponse[]' => false,
        ];
    }
}
