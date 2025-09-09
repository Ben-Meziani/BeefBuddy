<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private CacheInterface $cache,
    ) {}

    public function index(int $id): JsonResponse
    {
        $user = $this->cache->get('user_'.$id, function(ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);
            return $this->userRepository->find($id);
        });

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

    public function delete(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'User deleted successfully'], 200);
    }
}
