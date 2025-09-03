<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserRegistrationService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher,
        private JWTTokenManagerInterface $jwtManager,
        private EmailService $emailService,
    ) {}
    public function register(Request $request, string $hostFront)
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();
        $user->setUsername($data['username']);
        $user->setRoles(['ROLE_USER']);
        $user->setEmail($data['email']);
        // Here you would typically hash the password before saving it
        $hashedPassword = $this->passwordHasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);
        $user->setIsVerified(false);
        $this->entityManager->persist($user);

        // actually executes the queries (i.e. the INSERT query)
        $this->entityManager->flush();
        $token = $this->jwtManager->create($user);
        $url = $hostFront . '/login?token=' . $token;
        $this->emailService->sendEmail(
            $user,
            'Please Confirm your Email',
            'registration/confirmation_email.html.twig',
            [
            'confirmationUrl' => $url,
            'user' => $user,
            ]
        );
        return new JsonResponse(['message' => 'Registration successful. Please check your email to verify your account.'], 200);
    }
}
