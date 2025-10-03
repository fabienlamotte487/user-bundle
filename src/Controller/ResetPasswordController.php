<?php

namespace App\Controller;

use App\DTO\ResetPasswordDTO;
use App\Entity\User;
use App\Form\ChangePasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
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

    /**
     * Confirmation page after a user has requested a password reset.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Generate a fake token if the user does not exist or someone hit this page directly.
        // This prevents exposing whether or not a user was found with the given email address or not
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken();
        }

        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    // /**
    //  * Validates and process the reset URL that the user clicked in their email.
    //  */
    // #[Route('/reset/{token}', name: 'app_reset_password')]
    // public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, ?string $token = null): Response
    // {
    //     if ($token) {
    //         // We store the token in session and remove it from the URL, to avoid the URL being
    //         // loaded in a browser and potentially leaking the token to 3rd party JavaScript.
    //         $this->storeTokenInSession($token);

    //         return $this->redirectToRoute('app_reset_password');
    //     }

    //     $token = $this->getTokenFromSession();

    //     if (null === $token) {
    //         throw $this->createNotFoundException('No reset password token found in the URL or in the session.');
    //     }

    //     try {
    //         /** @var User $user */
    //         $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
    //     } catch (ResetPasswordExceptionInterface $e) {
    //         $this->addFlash('reset_password_error', sprintf(
    //             '%s - %s',
    //             ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE,
    //             $e->getReason()
    //         ));

    //         return $this->redirectToRoute('app_forgot_password_request');
    //     }

    //     // The token is valid; allow the user to change their password.
    //     $form = $this->createForm(ChangePasswordFormType::class);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // A password reset token should be used only once, remove it.
    //         $this->resetPasswordHelper->removeResetRequest($token);

    //         /** @var string $plainPassword */
    //         $plainPassword = $form->get('plainPassword')->getData();

    //         // Encode(hash) the plain password, and set it.
    //         $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
    //         $this->entityManager->flush();

    //         // The session is cleaned up after the password has been changed.
    //         $this->cleanSessionAfterReset();

    //         return $this->redirectToRoute('app_home');
    //     }

    //     return $this->render('reset_password/reset.html.twig', [
    //         'resetForm' => $form,
    //     ]);
    // }

    #[Route('/reset', name: 'api_reset_password', methods: ['POST'])]
    public function resetPasswordApi(
        Request $request,
        ValidatorInterface $validator,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        $token = $data['token'] ?? null;
        $plainPassword = $data['plainPassword'] ?? null;

        if (!$token || !$plainPassword) {
            return $this->json([
                'success' => false,
                'message' => 'Token et mot de passe sont requis.'
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            /** @var User $user */
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            return $this->json([
                'success' => false,
                'message' => sprintf('%s - %s',
                    ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE,
                    $e->getReason()
                )
            ], Response::HTTP_BAD_REQUEST);
        }

        $dto = new ResetPasswordDTO();
        $dto->plainPassword = $plainPassword;
        $dto->token = $token;
        $errors = $validator->validate($dto);

        if (count($errors) > 0) {
            $messages = [];
            foreach ($errors as $error) {
                $messages[] = $error->getMessage();
            }

            return $this->json([
                'success' => false,
                'message' => implode(' ', $messages)
            ], Response::HTTP_BAD_REQUEST);
        }

        // On supprime le reset request car le token ne doit servir qu’une fois
        $this->resetPasswordHelper->removeResetRequest($token);

        // On hash le nouveau mot de passe
        $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
        $this->entityManager->flush();

        // Nettoyage session (facultatif en API)
        $this->cleanSessionAfterReset();

        return $this->json([
            'success' => true,
            'message' => 'Mot de passe changé avec succès.'
        ]);
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

        // // Store the token object in session for retrieval in check-email route.
        // $this->setTokenObjectInSession($resetToken);

        return $this->json([
            'message' => 'Vérifiez votre e-mail pour votre lien de réinitialisation.',
        ]);
    }

    #[Route('/redirect/reset/{token}', name: 'api_users_reset_password_redirect', methods: ['GET'])]
    public function redirectApp(string $token)
    {
        return $this->redirect('mythictournament://resetPassword/' . $token);
    }
}
