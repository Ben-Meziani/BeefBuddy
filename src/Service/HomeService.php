<?php

namespace App\Service;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class HomeService
{
    public function __construct(
        private JWTEncoderInterface $jwtEncoder,
        private EntityManagerInterface $entityManager
    ) {}
    public function index(Request $request)
    {
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse(['error' => 'Token manquant ou invalide'], 400);
        }
        $token = str_replace('Bearer ', '', $authHeader);
        $decoded = $this->jwtEncoder->decode($token);
        $user = $this->entityManager->getRepository(User::class)->find($decoded['id']);
        if (!$user) {
            return new JsonResponse(['user' => 'guest'], 200);
        }

        return new JsonResponse(['user' => $user->getUsername(), 'roles' => $user->getRoles()], 200);
    }
}
