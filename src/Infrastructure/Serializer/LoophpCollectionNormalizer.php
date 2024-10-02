<?php

declare(strict_types=1);

namespace DeadlockHub\Infrastructure\Serializer;

use loophp\collection\Collection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class LoophpCollectionNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public const string INNER_TYPE = 'inner_type';

    public function __construct(
        #[Autowire(service: 'serializer.normalizer.object')]
        private readonly AbstractNormalizer $normalizer,
    ) {
    }

    /**
     * @return array<class-string, null|bool>
     */
    #[\Override]
    public function getSupportedTypes(?string $format): array
    {
        return [
            Collection::class => true,
        ];
    }

    #[\Override]
    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $denormalizedData = [];
        foreach ($data as $entry) {
            $denormalizedData[] = $this->normalizer->denormalize($entry, $context[self::INNER_TYPE], $format, $context);
        }

        return Collection::fromIterable($denormalizedData);
    }

    #[\Override]
    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return $type === Collection::class;
    }

    #[\Override]
    public function normalize(mixed $object, ?string $format = null, array $context = []): array|string|int|float|bool|\ArrayObject|null
    {
        $result = [];

        foreach ($object as $item) {
            $serializedItem = $this->normalizer->normalize($item, $format, $context);
            $result[] = $serializedItem;
        }

        return $result;
    }

    #[\Override]
    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Collection;
    }
}
