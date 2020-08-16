<?php


namespace App\DataFixtures;


use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ProductFixtures extends Fixture
{


    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');

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