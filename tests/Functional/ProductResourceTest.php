<?php


namespace App\Tests\Functional;

use App\Entity\Product;
use App\Test\CustomApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

class ProductResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testGetExistingSingleProduct()
    {
        $client = self::createClient();
        $user = $this->createUser('user1', 'user1@example.com', 'foo');
        $product = new Product();
        $product->setName('banana');
        $product->setKcal(100);
        $product->setOwner($user);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        $client->request('GET', '/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'kcal' => 100,
            'name' => 'banana'
        ]);
    }

    public function testGetNotExistingSingleProduct()
    {
        $client = self::createClient();
        $client->request('GET', '/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJsonContains([
            "detail" => "No product found with id 1",
            "status" => 404,
            "type" => "about:blank",
            "title" => "Not Found"
        ]);
    }

    public function testPostProductWhenUnauthorized()
    {
        $client = self::createClient();
        $client->request('POST', '/products', [
            'json' => [
                'name' => 'banana',
                'kcal' => 100
            ],
        ]);
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            "code" => 401,
            "message" => "JWT Token not found"
        ]);
    }

    public function testPostProductWhenAuthorized()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('POST', '/products', [
            'json' => [
                'name' => 'banana',
                'kcal' => 100
            ],
        ]);
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            "info" => 'Product has been added'
        ]);
    }

    public function testDeleteNotExistingProductWhenUnauthorized()
    {
        $client = self::createClient();
        $client->request('DELETE', '/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            "code" => 401,
            "message" => "JWT Token not found"
        ]);
    }

    public function testDeleteNotExistingProductWhenAuthorized()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('DELETE', '/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertJsonContains([
            "status" => 403,
            "type" => "about:blank",
            "title" => "Forbidden",
            "detail" => "Access Denied."
        ]);
    }

    public function testDeleteExistingProductWhenUserIsOwner()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $product = new Product();
        $product->setName('banana');
        $product->setKcal(100);
        $product->setOwner($user);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        $client->request('DELETE', '/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "info" => "Product has been deleted"
        ]);

    }

    public function testDeleteExistingProductWhenUserIsNotOwner()
    {
        $client = self::createClient();
        $user1 = $this->createUser( 'user1', 'user1@example.com', 'foo');
        $product = new Product();
        $product->setName('banana');
        $product->setKcal(100);
        $product->setOwner($user1);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        $user2 = $this->createUserAndLogin($client, 'user2', 'user2@example.com', 'foo');
        $client->request('DELETE', '/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertJsonContains([
            "status" => 403,
            "type" => "about:blank",
            "title" => "Forbidden",
            "detail" => "Access Denied."
        ]);
    }

    public function testDeleteExistingProductWhenUserIsAdmin()
    {
        $client = self::createClient();
        $user1 = $this->createUser( 'user1', 'user1@example.com', 'foo');
        $user2 = $this->createUser( 'user2', 'user2@example.com', 'foo');
        $product = new Product();
        $product->setName('banana');
        $product->setKcal(100);
        $product->setOwner($user1);
        $this->getEntityManager()->persist($product);
        $user2->setRoles(['ROLE_ADMIN']);
        $this->getEntityManager()->flush();
        $this->login($client, 'user2', 'foo');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $client->request('DELETE', '/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "info" => "Product has been deleted"
        ]);
    }

    public function testPatchNotExistingProductWhenUnauthorized()
    {
        $client = self::createClient();
        $client->request('PATCH', '/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            "code" => 401,
            "message" => "JWT Token not found"
        ]);
    }

    public function testPatchNotExistingProductWhenAuthorized()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('DELETE', '/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertJsonContains([
            "status" => 403,
            "type" => "about:blank",
            "title" => "Forbidden",
            "detail" => "Access Denied."
        ]);
    }
    public function testPatchExistingProductWhenUserIsOwner()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $product = new Product();
        $product->setName('banana');
        $product->setKcal(100);
        $product->setOwner($user);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        $client->request('PATCH', '/products/1', [
            'json' => [
                'name' => 'apple',
                'kcal' => 200
            ]]);
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "info" => "Product has been updated"
        ]);
    }
    public function testPatchExistingProductWhenUserIsNotOwner()
    {
        $client = self::createClient();
        $user1 = $this->createUser( 'user1', 'user1@example.com', 'foo');
        $product = new Product();
        $product->setName('banana');
        $product->setKcal(100);
        $product->setOwner($user1);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        $this->createUserAndLogin($client, 'user2', 'user2@example.com', 'foo');
        $client->request('PATCH', '/products/1', [
            'json' => [
                'name' => 'apple',
                'kcal' => 200
            ]]);
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertJsonContains([
            "status" => 403,
            "type" => "about:blank",
            "title" => "Forbidden",
            "detail" => "Access Denied."
        ]);
    }
    public function testPatchExistingProductWhenUserIsAdmin()
    {
        $client = self::createClient();
        $user1 = $this->createUser( 'user1', 'user1@example.com', 'foo');
        $user2 = $this->createUser( 'user2', 'user2@example.com', 'foo');
        $product = new Product();
        $product->setName('banana');
        $product->setKcal(100);
        $product->setOwner($user1);
        $this->getEntityManager()->persist($product);
        $user2->setRoles(['ROLE_ADMIN']);
        $this->getEntityManager()->flush();
        $this->login($client, 'user2', 'foo');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $client->request('PATCH', '/products/1', [
            'json' => [
                'name' => 'apple',
                'kcal' => 200
            ]]);
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "info" => "Product has been updated"
        ]);
    }

    public function testPostValidation()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('POST', '/products', [
            'json' => [
                'name' => '',
                'kcal' => ''
            ],
        ]);
        //dd($client->getResponse()->getContent(false));
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonContains([
            "status" => 400,
            "type" => "validation_error",
            "title" => "There was a validation error.",
            "errors" => [
                "name" => ["This value should not be blank."],
                "kcal" => ["This value should not be blank."]
            ]
        ]);
        $client->request('POST', '/products', [
            'json' => [
                'name' => '$',
                'kcal' => '$'
            ],
        ]);

        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonContains([
            "status" => 400,
            "type" => "validation_error",
            "title" => "There was a validation error.",
            "errors" => [
                "name" => [
                    "This value is too short. It should have 3 characters or more.",
                    "This value should be of type alnum.",

                ],
                "kcal" => [
                    "This value is not valid.",
                ]
            ]
        ]);
        $client->request('POST', '/products', [
            'json' => [
                'name' => 'a1',
                'kcal' => '-1'
            ],
        ]);
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonContains([
            "status" => 400,
            "type" => "validation_error",
            "title" => "There was a validation error.",
            "errors" => [
                "name" => [
                    "This value is too short. It should have 3 characters or more.",
                ],
                "kcal" => [
                    "This value should be greater than or equal to 0.",
                ]
            ]
        ]);
    }

    public function testPatchValidation()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $product = new Product();
        $product->setName('banana');
        $product->setKcal(100);
        $product->setOwner($user);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        $client->request('PATCH', '/products/1', [
            'json' => [
                'name' => '',
                'kcal' => ''
            ],
        ]);
        //dd($client->getResponse()->getContent(false));
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonContains([
            "status" => 400,
            "type" => "validation_error",
            "title" => "There was a validation error.",
            "errors" => [
                "name" => ["This value should not be blank."],
                "kcal" => ["This value should not be blank."]
            ]
        ]);
        $client->request('PATCH', '/products/1', [
            'json' => [
                'name' => '$',
                'kcal' => '$'
            ],
        ]);

        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonContains([
            "status" => 400,
            "type" => "validation_error",
            "title" => "There was a validation error.",
            "errors" => [
                "name" => [
                    "This value is too short. It should have 3 characters or more.",
                    "This value should be of type alnum.",

                ],
                "kcal" => [
                    "This value is not valid.",
                ]
            ]
        ]);
        $client->request('PATCH', '/products/1', [
            'json' => [
                'name' => 'a1',
                'kcal' => '-1'
            ],
        ]);
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonContains([
            "status" => 400,
            "type" => "validation_error",
            "title" => "There was a validation error.",
            "errors" => [
                "name" => [
                    "This value is too short. It should have 3 characters or more.",
                ],
                "kcal" => [
                    "This value should be greater than or equal to 0.",
                ]
            ]
        ]);
    }

    public function testPostInvalidJson()
    {
        $invalidJson = <<<EOF
{
    "name" "example",
    "kcal" : "2
    
}
EOF;
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'example','example@example.com', 'Password123');
        $client->request('POST', '/products', [
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

    public function testPatchInvalidJson()
    {
        $invalidJson = <<<EOF
{
    "name" "example",
    "kcal" : "2
    
}
EOF;
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $product = new Product();
        $product->setName('banana');
        $product->setKcal(100);
        $product->setOwner($user);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        $client->request('PATCH', '/products/1', [
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

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }

}