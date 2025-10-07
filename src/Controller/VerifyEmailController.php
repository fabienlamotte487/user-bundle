<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use App\Security\SendEmailVerifier;

final class VerifyEmailController extends AbstractController
{
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
