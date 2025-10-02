<?php 

namespace App\Manager;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserManager
{
    public function __construct(
        private EntityManagerInterface $em,
        private UserFactory $userFactory,
        private UserRepository $userRepository,
        private ValidatorInterface $validator,
    ) {}

    public function create(string $email, string $pseudo, string $plainPassword): User
    {

        $user = $this->userFactory->create($email, $pseudo, $plainPassword);
        
        $errors = $this->validator->validate($user, null, ['create']);
        if (count($errors) > 0) {
            throw new ValidationFailedException($user, $errors);
        }

        $user = $this->userFactory->setPassword($user);
        $this->userRepository->save($user, true);

        return $user;
    }

    public function delete(User $user): void
    {
        $this->userRepository->remove($user, true);
    }

    public function update(User $user, Request $request): User
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['pseudo'])) {
            $user->setPseudo($data['pseudo']);
        }

        $errors = $this->validator->validate($user, null, ['update']);
        if (count($errors) > 0) {
            throw new ValidationFailedException($user, $errors);
        }

        $this->userRepository->save($user, true);

        return $user;
    }

    public function get(User $user): array
    {
        return $this->userFactory->getFormatedInfo($user);
    }
}