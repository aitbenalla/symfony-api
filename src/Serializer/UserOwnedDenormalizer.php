<?php

namespace App\Serializer;

use App\Entity\UserOwnedInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Exception\RuntimeException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

class UserOwnedDenormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;
    private const ALREADY_CALLED_DENORMALIZER = 'UserOwnedDenormalizerCalled';

    public function __construct(private Security $security)
    {
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        $alreadyCalled = $context[$this->getAlReadyCalledKey($type)] ?? false;
//        $alreadyCalled = $data[self::ALREADY_CALLED_DENORMALIZER] ?? false;
        $reflectionClass = new \ReflectionClass($type);
        return $reflectionClass->implementsInterface(UserOwnedInterface::class) && $alreadyCalled === false;

    }

    public function denormalize(mixed $data, string $type, string $format = null, array $context = [])
    {
        $context[$this->getAlReadyCalledKey($type)] = true;
//        $data[self::ALREADY_CALLED_DENORMALIZER] = true;
        $obj = $this->denormalizer->denormalize($data, $type, $format, $context);
        $obj->setUser($this->security->getUser());
        return $obj;
    }

    private function getAlReadyCalledKey(string $type)
    {
        return self::ALREADY_CALLED_DENORMALIZER . $type;
    }
}