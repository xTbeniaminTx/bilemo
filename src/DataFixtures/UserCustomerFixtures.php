<?php

namespace App\DataFixtures;


use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCustomerFixtures extends Fixture implements DependentFixtureInterface
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
                ->setRoles($user->getRoles())
                ->setPassword($hash);

            $manager->persist($user);


            for ($c = 0; $c < mt_rand(5, 15); $c++) {
                $customer = new Customer();
                $customer->setFirstName($faker->firstName)
                    ->setLastName($faker->lastName)
                    ->setEmail($faker->email)
                    ->setPassword($faker->password)
                    ->setUser($user);
                $manager->persist($customer);
            }

        }


        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ProductFixtures::class
        ];
    }
}
