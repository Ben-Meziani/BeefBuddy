<?php

namespace App\Service;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
    ) {}

    public function index()
    {
        $user = $this->userRepository->find(49);

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        return new JsonResponse([
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'email' => $user->getEmail(),
            'is_verified' => $user->isVerified(),
        ]);
    }
}
