<?php

namespace App\Serializer\Normalizer;


use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserNormalizer implements ContextAwareNormalizerInterface
{
    private $router;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $context['groups'][] = 'user';
        $data = $this->normalizer->normalize($object, $format, $context);

        // Here, add, edit, or delete some data:
        $data['href']['self'] = $this->router->generate('users_get_item', [
            'id' => $object->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);
        $data['href']['products'] = $this->router->generate('users_products', [
            'id' => $object->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof User;
    }
}