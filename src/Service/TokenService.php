<?php

namespace App\Service;

use App\Entity\RefreshToken;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TokenService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JWTTokenManagerInterface $jwtManager
    ) {}

    public function refreshToken(Request $request)
    {
        $refreshToken = $request->cookies->get('refresh_token');
        if (!$refreshToken) {
            return new JsonResponse(['error' => 'Refresh token not found'], 404);
        }
        $refreshToken = $this->entityManager->getRepository(RefreshToken::class)->findOneBy(['token' => $refreshToken]);
        if(!$refreshToken || $refreshToken->getExpiresAt() < new \DateTimeImmutable()) {
            return new JsonResponse(['error' => 'Refresh token invalid or expired'], 401);
        }
        $user = $refreshToken->getUser();
        if(!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $xsrfToken = bin2hex(random_bytes(64));
        $payload = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'xsrfToken' => $xsrfToken,
        ];
        $accessToken = $this->jwtManager->createFromPayload($user, $payload);
        $accessTokenCookie = Cookie::create('access_token', $accessToken)
            ->withHttpOnly(true)
            ->withSecure(true)
            ->withExpires((new \DateTime())->modify('+15 min'))
            ->withSameSite('None');
        $response = new JsonResponse([
            'accessTokenExpiresIn' => 900,
            'xsrfToken' => $xsrfToken,
            'id' => $user->getId(),
        ]);
        $response->headers->setCookie($accessTokenCookie);
        return $response;
    }
}
