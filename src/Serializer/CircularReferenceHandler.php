<?php


namespace App\Serializer;


use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

class CircularReferenceHandler
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
    public function __invoke($object)
    {
        switch ($object) {
            case $object instanceof Product:
                return $object;
            case $object instanceof User:
                return $this->router->generate('users_get_item', [
                    'id' => $object->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL);

        }
    }
}