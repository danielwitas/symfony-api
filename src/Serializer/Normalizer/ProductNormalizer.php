<?php

namespace App\Serializer\Normalizer;


use App\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ProductNormalizer implements ContextAwareNormalizerInterface
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

        $data['href']['self'] = $this->router->generate('products_get_item', [
            'id' => $object->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_PATH);
        $data['href']['owner'] = $this->router->generate('users_get_item', [
            'id' => $object->getOwner()->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_PATH);

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return false;
        return $data instanceof Product;
    }
}