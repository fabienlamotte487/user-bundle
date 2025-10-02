<?php 

namespace App\Factory;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFactory
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function create(string $email, string $pseudo, string $plainPassword): User
    {
        $user = new User();
        $user->setEmail($email);
        $user->setPseudo($pseudo);
        $user->setPlainPassword($plainPassword);

        return $user;
    }

    public function setPassword(User $user): User
    {
        $hashed = $this->passwordHasher->hashPassword($user, $user->getPlainPassword());
        $user->setPassword($hashed);
        return $user;
    }

    public function getFormatedInfo(User $user): array 
    {
        $informations = [
            'pseudo' => $user->getPseudo(),
            'email' => $user->getEmail()
        ];

        return $informations;
    }
}
