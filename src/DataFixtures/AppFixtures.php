<?php

namespace App\DataFixtures;

use App\Entity\AccessProvider;
use App\Entity\Customer;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

        for ($a = 0; $a < 5; $a++) {
            $accessProvider = new AccessProvider();
            $accessProvider->setName($faker->company)
                ->setLogin($faker->randomNumber(7))
                ->setPassword($faker->password);

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

        for ($p=0; $p< 30; $p++) {
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
