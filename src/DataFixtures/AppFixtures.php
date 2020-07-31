<?php

namespace App\DataFixtures;

use App\Entity\AccessProvider;
use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($u = 0; $u < 5; $u++) {
            $user = new User();

            $hash = $this->encoder->encodePassword($user, "password");

            $user->setLogin($faker->userName)
                ->setPassword($hash);

            $manager->persist($user);

            for ($a = 0; $a < 5; $a++) {
                $accessProvider = new AccessProvider();
                $accessProvider->setName($faker->company)
                    ->setLogin($faker->userName)
                    ->setPassword($faker->password)
                ->setUser($user);

                $manager->persist($accessProvider);

                for ($c = 0; $c < mt_rand(5, 15); $c++) {
                    $customer = new Customer();
                    $customer->setFirstName($faker->firstName)
                        ->setLastName($faker->lastName)
                        ->setEmail($faker->email)
                        ->setPassword($faker->password)
                        ->setAccessProvider($accessProvider);

                    $manager->persist($customer);
                }
            }
        }


        for ($p = 0; $p < 30; $p++) {
            $product = new Product();
            $product->setName($faker->safeHexColor)
                ->setColor($faker->safeColorName)
                ->setDescription($faker->paragraph)
                ->setPrice($faker->randomFloat(2, 100, 750))
                ->setReleaseAt($faker->dateTimeBetween('-6 months'));

            $manager->persist($product);
        }

        $manager->flush();
    }
}
