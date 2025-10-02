<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Gesdinet\JWTRefreshTokenBundle\Model\RefreshTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class LoginController extends AbstractController
{
    #[Route('/api/logout', name: 'api_logout', methods: ['POST'])]
    public function logout(
        RefreshTokenManagerInterface $refreshTokenManager,
        EntityManagerInterface $em
    ): JsonResponse {
        $user = $this->getUser();

        if ($user) {
            // Supprimer tous les refresh tokens liés à cet utilisateur
            $token = $refreshTokenManager->getLastFromUsername($user->getEmail(), 100);
            $em->remove($token);
            $em->flush();
        }

        return new JsonResponse(['message' => 'Déconnecté avec succès']);
    }
}
