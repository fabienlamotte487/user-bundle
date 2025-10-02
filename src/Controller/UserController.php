<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Manager\UserManager;
use Doctrine\ORM\EntityManagerInterface;
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
            return $this->json(['message' => 'Utilisateur ajouté', 'id' => $newUser->getId()], 201);
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

        return $this->json(['message' => 'Utilisateur supprimé avec succès'], 200);
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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $userManager->get($this->getUser());

        return $this->json([
            'message' => 'Voici les informations utilisateur',
            'user' => $user
        ], 200);
    }
}
