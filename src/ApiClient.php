<?php

declare(strict_types=1);

namespace Hyra\NzCompaniesOfficeLookup;

use Hyra\NzCompaniesOfficeLookup\Exception\ConnectionException;
use Hyra\NzCompaniesOfficeLookup\Exception\NumberInvalidException;
use Hyra\NzCompaniesOfficeLookup\Exception\NumberNotFoundException;
use Hyra\NzCompaniesOfficeLookup\Exception\UnexpectedResponseException;
use Hyra\NzCompaniesOfficeLookup\Model\AbstractResponse;
use Hyra\NzCompaniesOfficeLookup\Model\NzBusinessRegistryResponse;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SerializerExceptionInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class ApiClient implements ApiClientInterface
{
    private HttpClientInterface $client;

    public function __construct(
        private DenormalizerInterface $denormalizer,
        private ValidatorInterface $validator,
        HttpClientInterface $client,
        string $apiKey,
    ) {
        $this->client = $client->withOptions([
            'base_uri' => 'https://api.business.govt.nz/gateway/nzbn/v5/',
            'headers'  => [
                'Ocp-Apim-Subscription-Key' => $apiKey,
            ],
        ]);
    }

    public function lookupNumber(string $businessNumber): NzBusinessRegistryResponse
    {
        if (false === BusinessNumberValidator::isValidNumber($businessNumber)) {
            throw new NumberInvalidException();
        }

        try {
            $response = $this->client->request('GET', \sprintf('/entities/%s', $businessNumber))->getContent();
        } catch (HttpExceptionInterface | TransportExceptionInterface $e) {
            if (404 === $e->getCode()) {
                throw new NumberNotFoundException();
            }

            throw new ConnectionException(
                \sprintf('Unable to connect to the Companies House API: %s', $e->getMessage()),
                $e
            );
        }

        /** @var NzBusinessRegistryResponse $model */
        $model = $this->decodeResponse($response, NzBusinessRegistryResponse::class);

        return $model;
    }

    /**
     * @template T of AbstractResponse
     *
     * @psalm-param    class-string<T> $type
     *
     * @psalm-return   T
     *
     * @throws UnexpectedResponseException
     */
    private function decodeResponse(string $response, string $type): object
    {
        try {
            /** @psalm-var T $model */
            $model = $this->denormalizer->denormalize(\json_decode($response, true), $type, 'json');
        } catch (SerializerExceptionInterface $e) {
            throw new UnexpectedResponseException(
                \sprintf('Unable to deserialize response "%s": %s', $response, $e->getMessage()),
                $e
            );
        }

        $violations = $this->validator->validate($model);

        if (0 < \count($violations)) {
            $errors = \array_map(
                fn (ConstraintViolationInterface $violation) => $violation->getPropertyPath(),
                \iterator_to_array($violations)
            );

            throw new UnexpectedResponseException(
                \sprintf('Response contains errors "%s": %s', $response, \json_encode($errors))
            );
        }

        return $model;
    }
}
