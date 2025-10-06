<?php 

namespace App\Manager;

use App\Entity\User;
use App\Factory\UserFactory;
use App\Repository\UserRepository;
use App\Security\SendEmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Error;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mime\Address;
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
        private SendEmailVerifier $sendEmailVerifier
    ) {}

    public function create(?string $email, ?string $pseudo, ?string $plainPassword): User
    {

        $user = $this->userFactory->create($email, $pseudo, $plainPassword);
        
        $errors = $this->validator->validate($user, null);
        if (count($errors) > 0) {
            throw new ValidationFailedException($user, $errors);
        }

        $user = $this->userFactory->setPassword($user);
        $this->userRepository->save($user, true);
        
        $this->sendEmailVerifier->sendEmailConfirmation(
            'verify_email',
            $user, 
            (new TemplatedEmail())
                ->from(new Address('contact@fabienlamotte.fr', 'MythicTournament'))
                ->to($user->getEmail())
                ->subject('Veuillez confirmer votre adresse email')
                ->htmlTemplate('registration/confirmation_email.html.twig'));

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

    public function updateEmail(User $user, string $newEmail): void
    {
        // Vérifier unicité
        $existing = $this->em->getRepository(User::class)->findOneBy(['email' => $newEmail]);
        if ($existing) {
            throw new \Exception("Cette adresse email est déjà utilisée.");
        }

        // Marquer comme non vérifié
        $user->setEmail($newEmail);
        $user->setIsVerified(false);
        $this->userRepository->save($user, true);

        $this->sendEmailVerifier->sendEmailConfirmation(
            'verify_email', 
            $user, 
            (new TemplatedEmail())
                ->from(new Address('contact@fabienlamotte.fr', 'MythicTournament'))
                ->to($user->getEmail())
                ->subject('Veuillez confirmer votre adresse email')
                ->htmlTemplate('registration/confirmation_email.html.twig'));
    }
}