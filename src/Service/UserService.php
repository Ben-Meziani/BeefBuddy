<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\JsonPath\JsonCrawler;

class UserService
{
    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private CacheInterface $cache,
        private SerializerInterface $serializer,
        private EmailService $emailService,
    ) {}

    public function index(int $id): JsonResponse
    {
        $user = $this->cache->get('user_'.$id, function(ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);
            return $this->userRepository->find($id);
        });

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
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
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $this->entityManager->remove($user);
        $this->entityManager->flush();
        return new JsonResponse(['message' => 'User deleted successfully'], Response::HTTP_OK);
    }

    public function update(int $id, Request $request): JsonResponse
    {
        $this->cache->delete('user_'.$id);
        $user = $this->userRepository->find($id);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }
        $data = new JsonCrawler($request->getContent());
        $username = $data->find('$.params.params.username')[0];
        $email = $data->find('$.params.params.email')[0];
        $user->setUsername($username);
        $user->setEmail($email);
        $this->entityManager->flush();
        $this->emailService->sendEmail($user, 'User updated successfully', 'update/user_updated.html.twig', []);
        return new JsonResponse(['message' => 'User updated successfully'], Response::HTTP_OK);
    }
}
