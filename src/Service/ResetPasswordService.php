<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EmailService $emailService,
        private JWTTokenManagerInterface $jwtManager,
        private JWTEncoderInterface $jwtEncoder,
        private UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function resetPassword(
        Request $request
        ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $data['email'],
        ]);
        if ($user) {
            $payload = [
                'email' => $user->getEmail(),
                'reset_password' => true,
                'exp' => (new \DateTimeImmutable('+10 minutes'))->getTimestamp(),
            ];
            $token = $this->jwtManager->createFromPayload($user, $payload);
            $url = $_ENV['HOST_FRONT'] . '/reset-password-form?token=' . $token;

            $this->emailService->sendEmail(
                $user,
                'Reset your password',
                'reset_password/email.html.twig',
                [
                    'resetPasswordUrl' => $url,
                    'user' => $user,
                ]
            );
        }
        return new JsonResponse([
            'token' => $token,
            'success' => true,
            'message' => 'If an account with this email exists, you will receive an email to reset your password.'
        ]);
    }

    public function resetPasswordForm(
        Request $request
    ): JsonResponse {
       $data = json_decode($request->getContent(), true);
        $token = $request->headers->get('X-Reset-Token');
        if (!$token) {
            return new JsonResponse(['error' => 'Token manquant'], 400);
        }
            $decoded = $this->jwtEncoder->decode($token);
            // Vérification de l'expiration du token
            if (!isset($decoded['exp']) || time() > $decoded['exp']) {
                return new JsonResponse(['error' => 'Token expiré'], 401);
            }
            // Vérification de l'intention du token
            if (!isset($decoded['reset_password']) || $decoded['reset_password'] !== true) {
                return new JsonResponse(['error' => 'Token invalide pour cette action'], 400);
            }

            $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $decoded['email']]);

            if (!$user) {
                return new JsonResponse(['error' => 'Utilisateur introuvable'], 404);
            }

            if (empty($data['password'])) {
                return new JsonResponse(['error' => 'Mot de passe manquant'], 400);
            }

            $hashedPassword = $this->passwordHasher->hashPassword($user, $data['password']);
            $user->setPassword($hashedPassword);

            $this->entityManager->flush();

            return new JsonResponse(['success' => true, 'message' => 'Mot de passe réinitialisé avec succès']);
    }
}
