<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// #[Route('/reset-password')]

class ResetPasswordController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private EmailVerifier $emailVerifier
    ) {
        $this->entityManager = $entityManager;
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/reset-password', name: 'app_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request, JWTTokenManagerInterface $jwtManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $data['email'],
        ]);
        if ($user) {
            $token = $jwtManager->create($user);
            $url = $_ENV['HOST_FRONT'] . '/reset-password-form?token=' . $token;
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('mailer@example.com', 'AcmeMailBot'))
                    ->to($user->getEmail())
                    ->subject('Reset your password')
                    ->htmlTemplate('reset_password/email.html.twig')
                    ->context([
                        'resetPasswordUrl' => $url, // 👈 ajout de l'URL dans le contexte Twig
                        'user' => $user, // facultatif mais souvent utile
                    ])
            );
            }
        return new JsonResponse([
            'success' => true,
            'message' => 'If an account with this email exists, you will receive an email to reset your password.'
        ]);
    }

    #[Route('/reset-password-form', name: 'app_reset_password_form', methods: ['POST'])]
    public function resetPasswordForm(
        Request $request,
        JWTEncoderInterface $jwtEncoder,
        UserPasswordHasherInterface $passwordHasher,
        ): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $authHeader = $request->headers->get('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse(['error' => 'Token manquant ou invalide'], 400);
        }

        $token = str_replace('Bearer ', '', $authHeader);

        try {
            $decoded = $jwtEncoder->decode($token);

            $user = $this->entityManager->getRepository(User::class)->find($decoded['id']);
            if (!$user) {
                throw $this->createNotFoundException(
                    'No product found for id '.$decoded['id']
                );
            }
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $data['password']
            );
            $user->setPassword($hashedPassword);

            $this->entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Password reset successful']);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Token invalide : ' . $e->getMessage()], 401);
        }
        return new JsonResponse([
            'success' => true,
            'message' => 'Password reset successful'
        ]);
    }
}
