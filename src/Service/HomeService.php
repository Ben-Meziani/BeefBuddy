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
    
    public function index(Request $request): JsonResponse
    {
        $jwt = $request->cookies->get('access_token');
        $xsrfHeader = $request->headers->get('X-XSRF-TOKEN');
        if (!$jwt || !$xsrfHeader) {
            return new JsonResponse(['error' => 'Token ou XSRF manquant'], 403);
        }

        try {
            $decoded = $this->jwtEncoder->decode($jwt);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Token invalide'], 401);
        }

        if (!isset($decoded['xsrfToken']) || $decoded['xsrfToken'] !== $xsrfHeader) {
            return new JsonResponse(['error' => 'XSRF token invalide'], 403);
        }

        $user = $this->entityManager->getRepository(User::class)->find($decoded['id']);

        if (!$user) {
            return new JsonResponse(['user' => 'guest'], 200);
        }

        return new JsonResponse([
            'status' => 200,
            'user' => $user->getUsername(),
            'roles' => $user->getRoles()
        ]);
    }
}
