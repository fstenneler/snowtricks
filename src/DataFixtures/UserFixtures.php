<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    
    public function load(ObjectManager $manager)
    {
        for ($i = 1; $i <= 10; $i++) {

            $user = new User();

            $user->setEmail('user' . $i . '@orlinstreet.rocks');
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'azerty'
            ));
            $manager->persist($user);

        }

        $manager->flush();
    }
}
