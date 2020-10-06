<?php


namespace App\Tests\Functional;


use App\Entity\Product;
use App\Entity\Template;
use App\Entity\User;
use App\Test\CustomApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

class TemplateResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testGetExistingSingleTemplateWhenUnautohrized()
    {
        $client = self::createClient();
        $user = $this->createUser('user1', 'user1@example.com', 'foo');
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $client->request('GET', '/api/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'status' => 401,
            'detail' => 'Could not find JWT token',
            "type" => "jwt_token_not_found",
            "title" => "JWT token not found",
        ]);
    }

    public function testGetNotExistingSingleTemplateWhenUnauthorized()
    {
        $client = self::createClient();
        $client->request('GET', '/api/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'status' => 401,
            'detail' => 'Could not find JWT token',
            "type" => "jwt_token_not_found",
            "title" => "JWT token not found",
        ]);
    }

    public function testPostTemplateWhenUnauthorized()
    {
        $client = self::createClient();
        $client->request('POST', '/api/templates', [
            'json' => [
                'name' => 'example',
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

    public function testPostTemplateWhenAuthorized()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('POST', '/api/templates', [
            'json' => [
                'name' => 'example',
            ],
        ]);
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            "info" => 'Template has been added'
        ]);
    }

    public function testDeleteNotExistingTemplateWhenUnauthorized()
    {
        $client = self::createClient();
        $client->request('DELETE', '/api/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'status' => 401,
            'detail' => 'Could not find JWT token',
            "type" => "jwt_token_not_found",
            "title" => "JWT token not found",
        ]);
    }

    public function testDeleteNotExistingTemplateWhenAuthorized()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('DELETE', '/api/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJsonContains([
            "status" => 404,
            "type" => "about:blank",
            "detail" => "Template does not exist",
            "title" => "Not Found"
        ]);
    }

    public function testDeleteExistingTemplateWhenUserIsOwner()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $client->request('DELETE', '/api/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "info" => "Template has been deleted"
        ]);

    }

    public function testDeleteExistingTemplateWhenUserIsNotOwner()
    {
        $client = self::createClient();
        $user1 = $this->createUser('user1', 'user1@example.com', 'foo');
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user1);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $this->createUserAndLogin($client, 'user2', 'user2@example.com', 'foo');
        $client->request('DELETE', '/api/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertJsonContains([
            "status" => 403,
            "type" => "about:blank",
            "title" => "Forbidden",
            "detail" => "This template does not belong to you"
        ]);
    }

    public function testDeleteExistingTemplateWhenUserIsAdmin()
    {
        $client = self::createClient();
        $user1 = $this->createUser('user1', 'user1@example.com', 'foo');
        $user2 = $this->createUser('user2', 'user2@example.com', 'foo');
        $user2->setRoles(['ROLE_ADMIN']);
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user1);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $this->login($client, 'user2', 'foo');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $client->request('DELETE', '/api/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "info" => "Template has been deleted"
        ]);
    }

    public function testPatchNotExistingTemplateWhenUnauthorized()
    {
        $client = self::createClient();
        $client->request('PATCH', '/api/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'status' => 401,
            'detail' => 'Could not find JWT token',
            "type" => "jwt_token_not_found",
            "title" => "JWT token not found",
        ]);
    }

    public function testPatchNotExistingTemplateWhenAuthorized()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('DELETE', '/api/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJsonContains([
            "status" => 404,
            "type" => "about:blank",
            "detail" => "Template does not exist",
            "title" => "Not Found"
        ]);
    }

    public function testPatchExistingTemplateWhenUserIsOwner()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $client->request('PATCH', '/api/templates/1', [
            'json' => [
                'name' => 'updated',
            ]]);
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "info" => "Template has been updated"
        ]);
        $client->request('GET', '/api/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "name" => "updated"
        ]);
    }

    public function testPatchExistingTemplateWhenUserIsNotOwner()
    {
        $client = self::createClient();
        $user1 = $this->createUser('user1', 'user1@example.com', 'foo');
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user1);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $this->createUserAndLogin($client, 'user2', 'user2@example.com', 'foo');
        $client->request('PATCH', '/api/templates/1', [
            'json' => [
                'name' => 'updated'
            ]]);
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertJsonContains([
            "status" => 403,
            "type" => "about:blank",
            "title" => "Forbidden",
            "detail" => "This template does not belong to you"
        ]);
    }

    public function testPatchExistingTemplateWhenUserIsAdmin()
    {
        $client = self::createClient();
        $user1 = $this->createUser('user1', 'user1@example.com', 'foo');
        $user2 = $this->createUser('user2', 'user2@example.com', 'foo');
        $user2->setRoles(['ROLE_ADMIN']);
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user1);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $this->login($client, 'user2', 'foo');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $client->request('PATCH', '/api/templates/1', [
            'json' => [
                'name' => 'updated',
            ]]);
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "info" => "Template has been updated"
        ]);
    }

    public function testTemplatePostValidation()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('POST', '/api/templates', [
            'json' => [
                'name' => '',
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
                    "This value should not be blank."
                ],
            ]
        ]);
        $client->request('POST', '/api/templates', [
            'json' => [
                'name' => '$$$'
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
                    "Name must contain only letters and numbers",
                ]
            ]
        ]);
        $client->request('POST', '/api/templates', [
            'json' => [
                'name' => 'a1'
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
                ]
            ]
        ]);
    }

    public function testTemplatePatchValidation()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $client->request('PATCH', '/api/templates/1', [
            'json' => [
                'name' => '',
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
                    "This value should not be blank."
                ],
            ]
        ]);
        $client->request('PATCH', '/api/templates/1', [
            'json' => [
                'name' => '$$$'
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
                    "Name must contain only letters and numbers",
                ]
            ]
        ]);
        $client->request('PATCH', '/api/templates/1', [
            'json' => [
                'name' => 'a1'
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
                ]
            ]
        ]);
    }

    public function testPostTemplateInvalidJson()
    {
        $invalidJson = <<<EOF
{
    "name" "example",
    
}
EOF;
        $client = self::createClient();
        $this->createUserAndLogin($client, 'example', 'example@example.com', 'Password123');
        $client->request('POST', '/api/templates', [
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

    public function testPatchTemplateInvalidJson()
    {
        $invalidJson = <<<EOF
{
    "name" "example"
    
}
EOF;
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $client->request('PATCH', '/api/templates/1', [
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

    public function testPostProductToTemplateWhenUnauthorized()
    {
        $client = self::createClient();
        $user = $this->createUser('user1', 'user1@example.com', 'foo');
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $client->request('POST', '/api/templates/1/products', [
            'json' => [
                'name' => 'example',
            ],
        ]);
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            'status' => 401,
            'type' => 'jwt_token_not_found',
            'title' => 'JWT token not found',
            'detail' => 'Could not find JWT token',
        ]);
    }

    public function testPostProductToTemplateWhenAuthorized()
    {
        $client = self::createClient();
        $user = $this->createUser('user1', 'user1@example.com', 'foo');
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $this->createUserAndLogin($client, 'user2', 'user2@example.com', 'foo');
        $client->request('POST', '/api/templates/1/products', [
            'json' => [
                'name' => 'example',
            ],
        ]);
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertJsonContains([
            "status" => 403,
            "type" => "about:blank",
            "title" => "Forbidden",
            "detail" => "This template does not belong to you"
        ]);
    }

    public function testPostProductToTemplateWhenUserIsOwner()
    {
        $client = self::createClient();
        $user = $this->createUser('user1', 'user1@example.com', 'foo');
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $this->login($client, 'user1', 'foo');
        $client->request('POST', '/api/templates/1/products', [
            'json' => [
                'name' => 'banana',
                'kcal' => 100,
                'protein' => 1,
                'carbs' => 1,
                'fat' => 1,
            ],
        ]);
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertJsonContains([
            "info" => "Product has been added"
        ]);
    }

    public function testGetProductCollectionFromTemplateWhenUserIsOwner()
    {
        $client = self::createClient();
        $user = $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $product1 = $this->createProduct('banana', 1, 1, 1, 1, $user, $template);
        $product2 = $this->createProduct('apple', 2, 2, 2, 2, $user, $template);
        $product3 = $this->createProduct('orange', 3, 3, 3, 3, $user, $template);
        $client->request('GET', '/api/templates/1/products');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            [
                'name' => 'banana',
                'kcal' => 1,
                'weight' => 100,
                'protein' => 1,
                'carbs' => 1,
                'fat' => 1,
            ],
            [
                'name' => 'apple',
                'kcal' => 2,
                'weight' => 100,
                'protein' => 2,
                'carbs' => 2,
                'fat' => 2,
            ],
            [
                'name' => 'orange',
                'kcal' => 3,
                'weight' => 100,
                'protein' => 3,
                'carbs' => 3,
                'fat' => 3,
            ],
        ]);
    }

    protected function getEntityManager(): EntityManagerInterface
    {
        return self::$container->get('doctrine')->getManager();
    }

    protected function createProduct(
        string $name, int $kcal, int $protein, int $carbs, int $fat, User $owner, Template $template = null
    ): Product
    {
        $product = new Product();
        $product->setName($name);
        $product->setKcal($kcal);
        $product->setProtein($protein);
        $product->setCarbs($carbs);
        $product->setFat($fat);
        $product->setWeight(100);
        $product->setOwner($owner);
        $product->setTemplate($template);
        $this->getEntityManager()->persist($product);
        $this->getEntityManager()->flush();
        return $product;
    }
}