<?php


namespace App\Tests\Functional;


use App\Entity\Product;
use App\Entity\Template;
use App\Test\CustomApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Hautelook\AliceBundle\PhpUnit\ReloadDatabaseTrait;
use Symfony\Component\HttpFoundation\Response;

class TemplateResourceTest extends CustomApiTestCase
{
    use ReloadDatabaseTrait;

    public function testGetExistingSingleTemplate()
    {
        $client = self::createClient();
        $user = $this->createUser('user1', 'user1@example.com', 'foo');
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $client->request('GET', '/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            'name' => 'example'
        ]);
    }

    public function testGetNotExistingSingleTemplate()
    {
        $client = self::createClient();
        $client->request('GET', '/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $this->assertJsonContains([
            "detail" => "No template found with id 1",
            "status" => 404,
            "type" => "about:blank",
            "title" => "Not Found"
        ]);
    }

    public function testPostTemplateWhenUnauthorized()
    {
        $client = self::createClient();
        $client->request('POST', '/templates', [
            'json' => [
                'name' => 'example',
            ],
        ]);
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            "code" => 401,
            "message" => "JWT Token not found"
        ]);
    }

    public function testPostTemplateWhenAuthorized()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('POST', '/templates', [
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
        $client->request('DELETE', '/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            "code" => 401,
            "message" => "JWT Token not found"
        ]);
    }

    public function testDeleteNotExistingTemplateWhenAuthorized()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('DELETE', '/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertJsonContains([
            "status" => 403,
            "type" => "about:blank",
            "title" => "Forbidden",
            "detail" => "Access Denied."
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
        $client->request('DELETE', '/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "info" => "Template has been deleted"
        ]);

    }

    public function testDeleteExistingTemplateWhenUserIsNotOwner()
    {
        $client = self::createClient();
        $user1 = $this->createUser( 'user1', 'user1@example.com', 'foo');
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user1);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $this->createUserAndLogin($client, 'user2', 'user2@example.com', 'foo');
        $client->request('DELETE', '/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertJsonContains([
            "status" => 403,
            "type" => "about:blank",
            "title" => "Forbidden",
            "detail" => "Access Denied."
        ]);
    }

    public function testDeleteExistingTemplateWhenUserIsAdmin()
    {
        $client = self::createClient();
        $user1 = $this->createUser( 'user1', 'user1@example.com', 'foo');
        $user2 = $this->createUser( 'user2', 'user2@example.com', 'foo');
        $user2->setRoles(['ROLE_ADMIN']);
        $template = new Template();
        $template->setName('example');
        $template->setOwner($user1);
        $this->getEntityManager()->persist($template);
        $this->getEntityManager()->flush();
        $this->login($client, 'user2', 'foo');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $client->request('DELETE', '/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "info" => "Template has been deleted"
        ]);
    }

    public function testPatchNotExistingTemplateWhenUnauthorized()
    {
        $client = self::createClient();
        $client->request('PATCH', '/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
        $this->assertJsonContains([
            "code" => 401,
            "message" => "JWT Token not found"
        ]);
    }

    public function testPatchNotExistingTemplateWhenAuthorized()
    {
        $client = self::createClient();
        $this->createUserAndLogin($client, 'user1', 'user1@example.com', 'foo');
        $client->request('DELETE', '/templates/1');
        $this->assertResponseHasHeader('Content-Type', 'application/problem+json');
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        $this->assertJsonContains([
            "status" => 403,
            "type" => "about:blank",
            "title" => "Forbidden",
            "detail" => "Access Denied."
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
        $client->request('PATCH', '/templates/1', [
            'json' => [
                'name' => 'updated',
            ]]);
        $this->assertResponseHasHeader('Content-Type', 'application/json');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJsonContains([
            "info" => "Template has been updated"
        ]);
        $client->request('GET', '/templates/1');
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
        $client->request('PATCH', '/templates/1', [
            'json' => [
                'name' => 'updated'
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
        $client->request('PATCH', '/templates/1', [
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
        $client->request('POST', '/templates', [
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
        $client->request('POST', '/templates', [
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
        $client->request('POST', '/templates', [
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
        $client->request('PATCH', '/templates/1', [
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
        $client->request('PATCH', '/templates/1', [
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
        $client->request('PATCH', '/templates/1', [
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
        $client->request('POST', '/templates', [
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
        $client->request('PATCH', '/templates/1', [
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