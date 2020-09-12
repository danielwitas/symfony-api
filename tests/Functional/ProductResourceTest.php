<?php


namespace App\Tests\Functional;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Product;
use App\Entity\User;
use App\Test\CustomApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;

class ProductResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testCreateProduct()
    {
        $client = self::createClient();

        $client->request('POST', '/products', [
            'json' => [],
        ]);
        $this->assertResponseStatusCodeSame(401);

        $this->createUserAndLogin($client, 'daniel', 'daniel@example.com', 'Password123');

        $client->request('POST', '/products', [
            'json' => [],
        ]);
        $this->assertResponseStatusCodeSame(400);
    }

    public function testUpdateProduct()
    {
        $client = self::createClient();
        $user1 = $this->createUser('user1','user1@example.com', 'foo');
        $user2 = $this->createUser('user2','user2@example.com', 'foo');
        $product = new Product();
        $product->setOwner($user1);
        $product->setName('banana');
        $product->setKcal(100);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        $this->login($client, 'user2', 'foo');
        $client->request('PATCH', '/products/' . $product->getId(), [
            'json' => ['name' => 'apple', 'kcal' => 222]
        ]);
        $this->assertResponseStatusCodeSame(403);

        $this->login($client, 'user1', 'foo');
        $client->request('PATCH', '/products/' . $product->getId(), [
            'json' => ['name' => 'apple', 'kcal' => 111]
        ]);
        $this->assertResponseStatusCodeSame(200);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }
}