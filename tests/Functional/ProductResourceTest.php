<?php


namespace App\Tests\Functional;

use App\Entity\Product;
use App\Entity\User;
use App\Test\CustomApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

class ProductResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    private function createProduct(string $name, int $kcal, int $protein, int $carbs, int $fat, User $owner): Product
    {
        $product = new Product();
        $product->setName($name);
        $product->setKcal($kcal);
        $product->setProtein($protein);
        $product->setCarbs($carbs);
        $product->setFat($fat);
        $product->setWeight(100);
        $product->setOwner($owner);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        return $product;
    }

    public function testGetExistingSingleProductWhenUnauthorized()
    {
        $client = self::createClient();
        $user = $this->createUser('user1', 'user1@example.com', 'foo');
        $this->createProduct('banana', 1, 1, 1, 1, $user);
        $client->request('GET', '/api/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'status' => 401,
            'detail' => 'Could not find JWT token',
            "type" => "jwt_token_not_found",
            "title" => "JWT token not found",
        ]);
    }

    public function testGetExistingSingleProductWhenUserIsNotOwner()
    {
        $client = self::createClient();
        $user = $this->createUser('user1', 'user1@example.com', 'foo');
        $this->createProduct('banana', 1, 1, 1, 1, $user);
        $this->createUserAndLogin($client, 'user2', 'user2@example.com', 'foo');
        $client->request('GET', '/api/products/1');
        $response = $client->getResponse();
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertJsonContains([
            "status" => 403,
            "type" => "about:blank",
            "title" => "Forbidden",
            "detail" => "This product does not belong to you"
        ]);
    }

    public function testGetExistingSingleProductWhenOwner()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $this->createProduct('banana', 1, 1, 1, 1, $user);
        $client->request('GET', '/api/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "name" => 'banana',
            "kcal" => 1,
            "weight" => 100,
            "protein" => 1,
            "carbs" => 1,
            "fat" => 1,
        ]);
    }

    public function testGetNotExistingSingleProduct()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('GET', '/api/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJsonContains([
            "status" => 404,
            "type" => "about:blank",
            "detail" => "Product does not exist",
            "title" => "Not Found"
        ]);
    }

    public function testPostProductWhenUnauthorized()
    {
        $client = self::createClient();
        $client->request('POST', '/api/products', [
            'json' => [
                "name" => 'banana',
                "kcal" => 1,
                "protein" => 1,
                "carbs" => 1,
                "fat" => 1,
            ],
        ]);
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'status' => 401,
            'detail' => 'Could not find JWT token',
            "type" => "jwt_token_not_found",
            "title" => "JWT token not found",
        ]);
    }

    public function testPostProductWhenAuthorized()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('POST', '/api/products', [
            'json' => [
                "name" => 'banana',
                "kcal" => 1,
                "protein" => 1,
                "carbs" => 1,
                "fat" => 1,
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
        $client->request('DELETE', '/api/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'status' => 401,
            'detail' => 'Could not find JWT token',
            "type" => "jwt_token_not_found",
            "title" => "JWT token not found",
        ]);
    }

    public function testDeleteNotExistingProductWhenAuthorized()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('DELETE', '/api/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJsonContains([
            "status" => 404,
            "type" => "about:blank",
            "detail" => "Product does not exist",
            "title" => "Not Found"
        ]);
    }

    public function testDeleteExistingProductWhenUserIsOwner()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $this->createProduct('banana', 1, 1, 1, 1, $user);
        $client->request('DELETE', '/api/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "info" => "Product has been deleted"
        ]);

    }

    public function testDeleteExistingProductWhenUserIsNotOwner()
    {
        $client = self::createClient();
        $user1 = $this->createUser('user1', 'user1@example.com', 'foo');
        $this->createProduct('banana', 1, 1, 1, 1, $user1);
        $this->createUserAndLogin($client, 'user2', 'user2@example.com', 'foo');
        $client->request('DELETE', '/api/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertJsonContains([
            "status" => 403,
            "type" => "about:blank",
            "title" => "Forbidden",
            "detail" => "This product does not belong to you"
        ]);
    }

    public function testDeleteExistingProductWhenUserIsAdmin()
    {
        $client = self::createClient();
        $user1 = $this->createUser('user1', 'user1@example.com', 'foo');
        $user2 = $this->createUser('user2', 'user2@example.com', 'foo');
        $this->createProduct('banana', 1, 1, 1, 1, $user1);
        $user2->setRoles(['ROLE_ADMIN']);
        $this->getEntityManager()->flush();
        $this->login($client, 'user2', 'foo');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $client->request('DELETE', '/api/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "info" => "Product has been deleted"
        ]);
    }

    public function testPatchNotExistingProductWhenUnauthorized()
    {
        $client = self::createClient();
        $client->request('PATCH', '/api/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'status' => 401,
            'type' => 'jwt_token_not_found',
            'title' => 'JWT token not found',
            'detail' => 'Could not find JWT token',
        ]);
    }

    public function testPatchNotExistingProductWhenAuthorized()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('DELETE', '/api/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJsonContains([
            "status" => 404,
            "type" => "about:blank",
            "detail" => "Product does not exist",
            "title" => "Not Found"
        ]);
    }

    public function testPatchExistingProductWhenUserIsOwner()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $this->createProduct('banana', 1, 1, 1, 1, $user);
        $client->request('PATCH', '/api/products/1', [
            'json' => [
                'name' => 'apple',
                'kcal' => 200
            ]]);
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'info' => 'Product has been updated'
        ]);
        $client->request('GET', '/api/products/1');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'name' => 'apple',
            'kcal' => 200
        ]);
    }

    public function testPatchExistingProductWhenUserIsNotOwner()
    {
        $client = self::createClient();
        $user1 = $this->createUser('user1', 'user1@example.com', 'foo');
        $this->createProduct('banana', 1, 1, 1, 1, $user1);
        $this->createUserAndLogin($client, 'user2', 'user2@example.com', 'foo');
        $client->request('PATCH', '/api/products/1', [
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
            "detail" => "This product does not belong to you"
        ]);
    }

    public function testPatchExistingProductWhenUserIsAdmin()
    {
        $client = self::createClient();
        $user1 = $this->createUser('user1', 'user1@example.com', 'foo');
        $user2 = $this->createUser('user2', 'user2@example.com', 'foo');
        $this->createProduct('banana', 1, 1, 1, 1, $user1);
        $user2->setRoles(['ROLE_ADMIN']);
        $this->getEntityManager()->flush();
        $this->login($client, 'user2', 'foo');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $client->request('PATCH', '/api/products/1', [
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

    public function testProductPostValidation()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('POST', '/api/products', [
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
                "kcal" => ["This value should not be blank."],
                "protein" => ["This value should not be blank."],
                "carbs" => ["This value should not be blank."],
                "fat" => ["This value should not be blank."],
            ]
        ]);
        $client->request('POST', '/api/products', [
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
                ],
                "kcal" => [
                    "This value is not valid.",
                ]
            ]
        ]);
        $client->request('POST', '/api/products', [
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

    public function testProductPatchValidation()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $this->createProduct('banana', 1, 1, 1, 1, $user);
        $client->request('PATCH', '/api/products/1', [
            'json' => [
                'name' => '',
                'kcal' => ''
            ],
        ]);
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
        $client->request('PATCH', '/api/products/1', [
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
                ],
                "kcal" => [
                    "This value is not valid.",
                ]
            ]
        ]);
        $client->request('PATCH', '/api/products/1', [
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

    public function testPostProductInvalidJson()
    {
        $invalidJson = <<<EOF
{
    "name" "example",
    "kcal" : "2
    
}
EOF;
        $client = self::createClient();
        $this->createUserAndLogin($client, 'example', 'example@example.com', 'Password123');
        $client->request('POST', '/api/products', [
            'json' => [
                $invalidJson
            ]
        ]);
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJsonContains([
            'status' => 400,
            'type' => 'validation_error',
            'title' => 'There was a validation error.'
        ]);

    }

    public function testPatchProductInvalidJson()
    {
        $invalidJson = <<<EOF
{
    "name" "example",
    "kcal" : "2
    
}
EOF;
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $this->createProduct('banana', 1, 1, 1, 1, $user);
        $client->request('PATCH', '/api/products/1', [
            'json' => [
                $invalidJson
            ]
        ]);
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
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