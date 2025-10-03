<?php 

namespace App\Manager;

use App\DTO\ResetPasswordDTO;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

class ResetPasswordManager
{
    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private UserPasswordHasherInterface $passwordHasher,
        private EntityManagerInterface $entityManager,
        private ValidatorInterface $validator
    ) {}

    /**
     * @throws BadRequestException si validation ou token échoue
     */
    public function resetPassword(ResetPasswordDTO $dto): User
    {
        // Validation du DTO
        $errors = $this->validator->validate($dto);
        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }
            throw new BadRequestException(implode(' ', $messages));
        }

        // Validation du token et récupération de l'utilisateur
        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($dto->token);
        } catch (ResetPasswordExceptionInterface $e) {
            throw new BadRequestException('Token invalide ou expiré.');
        }

        // Suppression du token
        $this->resetPasswordHelper->removeResetRequest($dto->token);

        // Hash et update du mot de passe
        $user->setPassword($this->passwordHasher->hashPassword($user, $dto->plainPassword));
        $this->entityManager->flush();

        return $user;
    }
}