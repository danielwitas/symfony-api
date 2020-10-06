<?php


namespace App\Tests\Functional;


use App\Test\CustomApiTestCase;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class UserResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testGetUserCollection()
    {
        $client = self::createClient();
        for($i = 0; $i < 25; $i++) {
            $this->createUser('example'.$i, 'example'.$i.'@example.com', 'Password123');
        }
        $this->login($client, 'example1', 'Password123');
        $client->request('GET', '/api/users');
        $this->assertResponseStatusCodeSame(200);
        $data = $client->getResponse()->getContent(false);
        $data =json_decode($data, true);
        $this->assertCount(10, $data['users']);
        $this->assertEquals($data['users'][5]['username'], 'example5');
        $this->assertEquals($data['total'], 25);
        $this->assertEquals($data['count'], 10);
        $this->assertArrayHasKey('links', $data);
        $this->assertArrayHasKey('self', $data['links']);
        $this->assertArrayHasKey('first', $data['links']);
        $this->assertArrayHasKey('last', $data['links']);
        $this->assertArrayHasKey('next', $data['links']);
    }


    public function test404Exception()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('GET', '/api/users/999');
        $this->assertResponseStatusCodeSame(404);
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertJsonContains([
            'status' => 404,
            'type' => 'about:blank',
            'title' => 'Not Found',
            'detail' => 'This user does not exist'
        ]);
    }


}