<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use App\Security\SendEmailVerifier;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{
    #[Route('/api/user', methods: ["POST"], name: "create_user")]
    public function createUser(
        Request $request, 
        UserManager $userManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $plainPassword = $data['plainPassword'] ?? null;
        $pseudo = $data['pseudo'] ?? null;

        try {
            $newUser = $userManager->create($email, $pseudo, $plainPassword);
            return $this->json([
                'message' => "Utilisateur ajouté ! Nous vous envoyons un mail pour confirmer votre compte",
                'user' => [
                    'id' => $newUser->getId(),
                    'pseudo' => $newUser->getPseudo(),
                    'email' => $newUser->getEmail(),
                    "created_at" => $newUser->getCreatedAt()
                ]
            ], 201);
        } catch (ValidationFailedException  $e) {
            $errors = [];
            foreach ($e->getViolations() as $violation) {
                $errors[] = $violation->getMessage();
            }

            return $this->json(['errors' => $errors], 400);
        }
    }

    #[Route('/api/user', name: 'delete_user', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function deleteUser(UserManager $userManager): JsonResponse
    {
        $userManager->delete($this->getUser());

        return $this->json(['message' => 'Utilisateur supprimé avec succès'], 204);
    }

    #[Route('/api/user', name: 'update_user', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function updateUser(UserManager $userManager, Request $request): JsonResponse
    {
        $user = $userManager->update($this->getUser(), $request);

        return $this->json([
            'message' => 'Utilisateur modifié avec succès',
            'user' => $userManager->get($user)
        ], 200);
    }

    #[Route('/api/user', name: 'get_user', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getUserInfo(UserManager $userManager): JsonResponse
    {
        $user = $userManager->get($this->getUser());

        return $this->json([
            'message' => 'Voici les informations utilisateur',
            'user' => $user
        ], 200);
    }

    #[Route('/api/user/{id}', name: 'get_target_user', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getUserTargetInfo(User $targetUser, UserManager $userManager): JsonResponse
    {
        $user = $userManager->get($targetUser);
        return $this->json([
            'message' => 'Voici les informations utilisateur',
            'user' => $user
        ], 200);
    }

    #[Route('/api/user/email', name: 'update_user_email', methods: ['PUT'])]
    #[IsGranted('ROLE_USER')]
    public function updateEmail(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        UserManager $userManager
    ): JsonResponse {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $this->getUser();

        $data = json_decode($request->getContent(), true);
        $newEmail = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$newEmail || !$password) {
            return $this->json(['error' => "Email et mot de passe sont requis"], 400);
        }

        // Vérification du mot de passe actuel
        if (!$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['error' => "Mot de passe incorrect"], 403);
        }

        try {
            $userManager->updateEmail($user, $newEmail);

            return $this->json([
                'message' => "Un email de confirmation a été envoyé à $newEmail. Veuillez cliquer sur le lien pour valider."
            ], 200);
        } catch (\Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    #[Route('/verify/email', name: 'verify_email', methods: ["GET"])]
    public function verifyUserEmail(
        Request $request, 
        UserRepository $userRepository, 
        SendEmailVerifier $sendEmailVerifier, 
    ): JsonResponse {
        $userId = $request->query->get('id');
        $user = $userRepository->find($userId);

        if (!$user) {
            return $this->json(['error' => 'Utilisateur non trouvé'], 404);
        }

        try {
            $sendEmailVerifier->handleEmailConfirmation($request, $user);
            return $this->json(['message' => 'Email confirmé avec succès !']);
        } catch (\Exception $e) {
            return $this->json(['error' => 'Lien invalide ou expiré'], 400);
        }
    }
}
