<?php

namespace App\Serializer\Normalizer;


use App\Annotation\Link;
use App\Entity\ApiEntityInterface;
use Doctrine\Common\Annotations\Reader;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class EntityNormalizer implements ContextAwareNormalizerInterface
{
    private $router;
    private $normalizer;
    private $annotationReader;
    private $expressionLanguage;

    public function __construct(
        UrlGeneratorInterface $router,
        ObjectNormalizer $normalizer,
        Reader $annotationReader
    )
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
        $this->annotationReader = $annotationReader;
        $this->expressionLanguage = new ExpressionLanguage();
    }

    public function normalize($object, string $format = null, array $context = [])
    {
        $context['groups'][] = 'user';
        $data = $this->normalizer->normalize($object, $format, $context);
        $annotations = $this->annotationReader->getClassAnnotations(new \ReflectionObject($object));
        $links = [];
        foreach ($annotations as $annotation) {
            if ($annotation instanceof Link) {
                $uri = $this->router->generate(
                    $annotation->route,
                    $this->resolveParams($annotation->params, $object)
                );
                $links[$annotation->name] = $uri;
            }
        }
        if(count($links)) {
            $data['links'] = $links;
        }

        return $data;
    }

    private function resolveParams(array $params, $object)
    {
        foreach ($params as $key => $param) {
            $params[$key] = $this->expressionLanguage
                ->evaluate($param, ['object' => $object]);
        }
        return $params;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $data instanceof ApiEntityInterface;
    }
}