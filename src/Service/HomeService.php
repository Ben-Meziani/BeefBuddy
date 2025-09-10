<?php

namespace App\Service;

// use App\Entity\User;
use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class HomeService
{
    public function __construct(
        private JWTEncoderInterface $jwtEncoder,
        private CacheInterface $cache,
        private UserRepository $userRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $jwt = $request->cookies->get('access_token');
        $xsrfHeader = $request->headers->get('X-XSRF-TOKEN');
        if (!$jwt || !$xsrfHeader) {
            return new JsonResponse(['error' => 'Token ou XSRF manquant'], 403);
        }

        $decoded = $this->jwtEncoder->decode($jwt);
        if (!$decoded) {
            return new JsonResponse(['error' => 'Token invalide'], 401);
        }

        if (!isset($decoded['xsrfToken']) || $decoded['xsrfToken'] !== $xsrfHeader) {
            return new JsonResponse(['error' => 'XSRF token invalide'], 403);
        }

        $user = $this->cache->get('user_'.$decoded['id'], function(ItemInterface $item) use ($decoded) {
            $item->expiresAfter(30);
            return $this->userRepository->find($decoded['id']);
        });

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
