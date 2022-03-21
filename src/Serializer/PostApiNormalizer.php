<?php

namespace App\Serializer;

use App\Entity\Post;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;

class PostApiNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'PostApiNormalizerCalled';

    public function __construct(private AuthorizationCheckerInterface $checker)
    {
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        $alreadyCalled = $context[self::ALREADY_CALLED] ?? false;
        return $data instanceof Post && $alreadyCalled === false;
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;
        if ($this->checker->isGranted('ROLE_USER') && isset($context['groups']))
            $context['groups'][] = 'read:posts:User';

        return $this->normalizer->normalize($object, $format, $context);
    }
}