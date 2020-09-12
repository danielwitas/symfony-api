<?php


namespace App\Test;


use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\Client;
use App\Entity\User;

class CustomApiTestCase extends ApiTestCase
{
    protected function createUser(string $username, string $email, string $password): User
    {
        $user = new User();
        $user->setUsername($username);
        $user->setEmail($email);
        $encoded = self::$container->get('security.password_encoder')
            ->encodePassword($user, $password);
        $user->setPassword($encoded);
        $em = self::$container->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
        return $user;
    }

    protected function login(Client $client, string $username, string $password) {
        $client->request('POST', '/login', [
            'json' => [
                'username' => $username,
                'password' => $password,
            ],
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    protected function createUserAndLogin(Client $client, string $username, string $email, string $password) {
        $user = $this->createUser($username, $email, $password);
        $this->login($client, $username, $password);
        return $user;
    }
}