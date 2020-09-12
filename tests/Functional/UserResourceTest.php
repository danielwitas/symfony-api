<?php


namespace App\Tests\Functional;


use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

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
        $this->assertResponseStatusCodeSame(201);
        $this->login($client, 'example', 'Password123');
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
//        $this->assertJsonContains([
//            $data[0]['username'] => 'example0'
//        ]);
        //dd($data);
        $this->assertCount(10, $data['users']);
        $this->assertEquals($data['users'][5]['username'], 'example5');
        $this->assertEquals($data['total'], 25);
        $this->assertEquals($data['count'], 10);
        $this->assertArrayHasKey('_links.next', $data);



    }

    public function testUserChangePassword()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'example','example@example.com', 'Password123');
        $client->request('PATCH', '/users/' . $user->getId() . '/change-password', [
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
            'title' => 'Not Found'
        ]);
    }


}