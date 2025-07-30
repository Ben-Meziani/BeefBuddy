<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(
        Request $request,
        JWTEncoderInterface $jwtEncoder,
        EntityManagerInterface $entityManager
    ): JsonResponse
    {
        // $token = $request->headers->get('Autorisation');
        $authHeader = $request->headers->get('Authorization');
        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return new JsonResponse(['error' => 'Token manquant ou invalide'], 400);
        }
        $token = str_replace('Bearer ', '', $authHeader);
        $decoded = $jwtEncoder->decode($token);
        $user = $entityManager->getRepository(User::class)->find($decoded['id']);
        if (!$user) {
            return $this->json([
                'user' => 'guest',
            ]);
        }

        return $this->json([
            'user' => $user->getUsername(),
            'roles' => $user->getRoles(),
        ]);
    }
}
