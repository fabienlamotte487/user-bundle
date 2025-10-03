<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use App\Security\SendEmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

final class UserController extends AbstractController
{
    #[Route('/api/user', methods: ["POST"], name: "create_user")]
    public function createUser(
        Request $request, 
        SendEmailVerifier $sendEmailVerifier,
        UserManager $userManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $email = $data['email'] ?? null;
        $plainPassword = $data['plainPassword'] ?? null;
        $pseudo = $data['pseudo'] ?? null;

        try {
            $newUser = $userManager->create($email, $pseudo, $plainPassword);
            $sendEmailVerifier->sendEmailConfirmation(
                'verify_email', 
                $newUser, 
                (new TemplatedEmail())
                    ->from('contact@fabienlamotte.fr')
                    ->to($newUser->getEmail())
                    ->subject('Veuillez confirmer votre adresse email')
                    ->htmlTemplate('registration/confirmation_email.html.twig'));

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
