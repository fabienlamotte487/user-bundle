<?php

namespace App\Controller;

use App\DTO\ResetPasswordDTO;
use App\Entity\User;
use App\Manager\ResetPasswordManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait;

    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper,
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Display & process form to request a password reset.
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer): Response
    {
        $email = json_decode($request->getContent(), true)['email'];

        return $this->processSendingPasswordResetEmail(
            $email,
            $mailer
        );
    }

    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer): JsonResponse
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Do not reveal whether a user account was found or not.
        if (!$user) {
            return $this->json([
                'message' => 'No user found for this email.',
            ], Response::HTTP_NOT_FOUND);
        }

        try {
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->json([
                'message' => 'There was a problem handling your password reset request.',
                'reason' => $e->getReason(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $email = (new TemplatedEmail())
            ->from(new Address('contact@fabienlamotte.fr', 'MythicTournament'))
            ->to((string) $user->getEmail())
            ->subject('Your password reset request')
            ->htmlTemplate('reset_password/email.html.twig')
            ->context([
                'resetToken' => $resetToken,
            ])
        ;

        $mailer->send($email);

        return $this->json([
            'message' => 'Vérifiez votre e-mail pour votre lien de réinitialisation.',
        ]);
    }

    #[Route('/redirect/reset/{token}', name: 'api_users_reset_password_redirect', methods: ['GET'])]
    public function redirectApp(string $token)
    {
        return $this->redirect('mythictournament://resetPassword/' . $token);
    }
    
    #[Route('/reset', name: 'api_reset_password', methods: ['POST'])]
    public function resetPassword(
        Request $request,
        ResetPasswordManager $manager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);

        $dto = new ResetPasswordDTO();
        $dto->plainPassword = $data['plainPassword'] ?? null;
        $dto->token = $data['token'] ?? null;

        try {
            $manager->resetPassword($dto);
        } catch (\Exception $e) {
            return $this->json([
                'success' => false,
                'message' => $e->getMessage()
            ], Response::HTTP_BAD_REQUEST);
        }

        return $this->json([
            'success' => true,
            'message' => 'Mot de passe changé avec succès.'
        ]);
    }
}
