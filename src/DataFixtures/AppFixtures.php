<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Template;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    private const USERS = [
        [
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => 'Password123',
            'roles' => [User::ROLE_ADMIN],
            'enabled' => true
        ],
        [
            'username' => 'jan_kowalski',
            'email' => 'jankowalski@example.com',
            'password' => 'Password123',
            'roles' => [User::ROLE_USER],
            'enabled' => true
        ],
        [
            'username' => 'robert_lewandowski',
            'email' => 'robert@example.com',
            'password' => 'Password123',
            'roles' => [User::ROLE_USER],
            'enabled' => true
        ],
        [
            'username' => 'cristiano_ronaldo',
            'email' => 'cristiano@bexample.com',
            'password' => 'Password123',
            'roles' => [User::ROLE_USER],
            'enabled' => true
        ],
        [
            'username' => 'leo_messi',
            'email' => 'leo@example.com',
            'password' => 'Password123',
            'roles' => [User::ROLE_USER],
            'enabled' => true
        ],
        [
            'username' => 'luis_figo',
            'email' => 'luis@example.com',
            'password' => 'Password123',
            'roles' => [User::ROLE_USER],
            'enabled' => true
        ],
    ];

    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
//        $this->loadTemplates($manager);
//        $this->loadProducts($manager);
    }

    public function loadProducts(ObjectManager $manager)
    {
        for($i = 0; $i < 300; $i++){
            $product = new Product();
            $product->setName('product_'.$i);
            $product->setKcal(random_int(1,100));
            $product->setProtein(random_int(1,100));
            $product->setCarbs(random_int(1,100));
            $product->setFat(random_int(1,100));
            $product->setOwner($this->getRandomUserReference());
            $manager->persist($product);
        }
        $manager->flush();
    }

    public function loadTemplates(ObjectManager $manager)
    {
        for($i = 0; $i < 500; $i++)
        {
            $template = new Template();
            $template->setName('template_'.$i);
            $template->setOwner($this->getRandomUserReference());
            $this->setReference("template_$i", $template);
            $manager->persist($template);
            for($j=0; $j<rand(1,5); $j++){
                $product = new Product();
                $product->setName('product_'.$j);
                $product->setKcal(random_int(1,100));
                $product->setKcal(random_int(1,100));
                $product->setProtein(random_int(1,100));
                $product->setCarbs(random_int(1,100));
                $product->setFat(random_int(1,100));
                $product->setOwner($template->getOwner());
                $product->setTemplate($template);
                $manager->persist($product);
            }
        }
        $manager->flush();
    }

    public function loadUsers(ObjectManager $manager)
    {
        foreach (self::USERS as $userFixture) {
            $user = new User();
            $user->setUsername($userFixture['username']);
            $user->setEmail($userFixture['email']);
            $user->setPassword($this->userPasswordEncoder->encodePassword(
                $user,
                $userFixture['password']
            ));
            $user->setRoles($userFixture['roles']);
            $user->setEnabled($userFixture['enabled']);

            $this->addReference('user_' . $userFixture['username'], $user);

            $manager->persist($user);
        }
        $manager->flush();
    }

    public function getRandomUserReference()
    {
        $randomUser = self::USERS[rand(0, 5)];

        return $this->getReference('user_' . $randomUser['username']);
    }
}
