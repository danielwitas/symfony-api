<?php


namespace App\EventListener;


use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;

class AuthenticationSubscriber implements EventSubscriberInterface
{

    private $secure = true;
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public static function getSubscribedEvents()
    {
        return [
            'lexik_jwt_authentication.on_authentication_success' => ['onAuthenticationSuccess']
        ];
    }

    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {

        $tokenExpireTime = $this->parameterBag->get('lexik_jwt_authentication.token_ttl');
        $response = $event->getResponse();
        $data = $event->getData();
        $token = $data['token'];
        $response->headers->setCookie(
            new Cookie(
                'BEARER',
                $token,
                (new \DateTime())->add(new \DateInterval('PT' . $tokenExpireTime . 'S')),
                '/',
                null,
                $this->secure
            )
        );
    }


}