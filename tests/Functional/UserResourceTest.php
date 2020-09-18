<?php


namespace App\Tests\Functional;


use App\Entity\User;
use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

class UserResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateUser()
    {
        $client = self::createClient();
        $client->request('POST', '/users', [
            'json' => [
                'username' => 'example',
                'email' => 'example@example.com',
                'password' => 'Password123',
                'repeatPassword' => 'Password123',
            ]
        ]);
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->login($client, 'example', 'Password123');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $em = self::$container->get('doctrine')->getManager();
        $user = $em->getRepository(User::class)->findOneBy(['username' => 'example']);
        $user->setEnabled(true);
        $em->flush();
        $this->login($client, 'example', 'Password123');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

    }

    public function testGetUserCollection()
    {
        $client = self::createClient();
        for($i = 0; $i < 25; $i++) {
            $this->createUser('example'.$i, 'example'.$i.'@example.com', 'Password123');
        }
        $client->request('GET', '/users');
        $this->assertResponseStatusCodeSame(200);
        //dd($client->getResponse()->getContent(false));
        $data = $client->getResponse()->getContent(false);
        $data =json_decode($data, true);
        $this->assertCount(10, $data['users']);
        $this->assertEquals($data['users'][5]['username'], 'example13');
        $this->assertEquals($data['total'], 25);
        $this->assertEquals($data['count'], 10);
        $this->assertArrayHasKey('links', $data);
        $this->assertArrayHasKey('self', $data['links']);
        $this->assertArrayHasKey('first', $data['links']);
        $this->assertArrayHasKey('last', $data['links']);
        $this->assertArrayHasKey('next', $data['links']);
    }

    public function testUserChangePassword()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'example','example@example.com', 'Password123');
        $client->request('PATCH', '/change-password', [
            'json' => [
                'password' => 'Password123',
                'newPassword' => 'Password1234',
                'repeatNewPassword' => 'Password1234'
            ]
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertJsonContains([
            'info' => 'Password has been changed.'
        ]);

    }

    public function testInvalidJson()
    {
        $invalidJson = <<<EOF
{
    "email" "example@example.com",
    "fakeNumber" : "2
    "fakeString": "I'm from a test!"
    
}
EOF;
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'example','example@example.com', 'Password123');
        $client->request('POST', '/users', [
            'json' => [
                $invalidJson
            ]
        ]);
        $this->assertJsonContains([
            'status' => 400,
            'type' => 'validation_error',
            'title' => 'There was a validation error.'
        ]);

    }

    public function test404Exception()
    {
        $client = self::createClient();
        $client->request('GET', '/users/999');
        $this->assertResponseStatusCodeSame(404);
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertJsonContains([
            'status' => 404,
            'type' => 'about:blank',
            'title' => 'Not Found',
            'detail' => 'No user found with id 999'
        ]);
    }


}