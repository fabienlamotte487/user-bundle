<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixturesTest extends Fixture
{
    private $datas = [
        [
            'email' => 'loginSuccess@example.fr',
            'password' => "Platinum#0000",
            'pseudo' => "Alcatraz",
            'verified' => true
        ],
        [
            'email' => 'loginErrorPassword@example.fr',
            'password' => "Platinum#0000",
            'pseudo' => "Alcatraz",
            'verified' => true
        ],
        [
            'email' => 'loginErrorNotVerified@example.fr',
            'password' => "Platinum#0000",
            'pseudo' => "Alcatraz",
            'verified' => false
        ]
    ];
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        foreach($this->datas as $data){
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPseudo($data['pseudo']);

            $password = $this->hasher->hashPassword($user, $data['password']);
            $user->setPassword($password);

            $user->setIsVerified($data['verified']);

            $manager->persist($user);
        }
        $manager->flush();
    }
}
