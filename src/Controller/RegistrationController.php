<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_registration', methods: ['POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        try{
        $data = json_decode($request->getContent(), true);
        $user = new User();
        $user->setUsername($data['username']);
        $user->setRoles(['ROLE_USER']);
        $user->setEmail($data['email']);
        // Here you would typically hash the password before saving it
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $data['password']
        );
        $user->setPassword($hashedPassword);
        $entityManager->persist($user);

        // actually executes the queries (i.e. the INSERT query)
        $entityManager->flush();

        return new JsonResponse($data, 200);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if (str_contains($message, 'UNIQ_IDENTIFIER_USERNAME')) {
                return new JsonResponse(['error' => 'Le username est déjà utilisé.'], 400);
            }
            if (str_contains($message, 'UNIQ_IDENTIFIER_EMAIL')) {
                return new JsonResponse(['error' => 'L\'email est déjà utilisé.'], 400);
            }

            return new JsonResponse(['error' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }
}
