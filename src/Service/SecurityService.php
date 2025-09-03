<?php

namespace App\Service;

use App\DTO\LoginData;
use App\Entity\RefreshToken;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
class SecurityService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private JWTTokenManagerInterface $jwtManager,
        private UserPasswordHasherInterface $passwordHasher,
        private SerializerInterface $serializer
    ) {}


    public function login(Request $request)
    {
        $data = $this->serializer->deserialize($request->getContent(), LoginData::class, 'json');

        $email = $data->email;
        $password = $data->password;
        if (!$email || !$password) {
            return new JsonResponse(['message' => 'missing_required_parameter'], Response::HTTP_BAD_REQUEST);
        }
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (!$user || !$this->passwordHasher->isPasswordValid($user, $password)) {
            return new JsonResponse(['message' => 'Username or password is incorrect'], Response::HTTP_UNAUTHORIZED);
        }
        $xsrfToken = bin2hex(random_bytes(64));
        $payload = [
            'id' => $user->getId(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'xsrfToken' => $xsrfToken,
        ];
        $accessToken = $this->jwtManager->createFromPayload($user, $payload);

        // Générer le refresh token et le stocker en base
        $refreshTokenString = base64_encode(random_bytes(128));

        $refreshToken = new RefreshToken();
        $refreshToken->setUser($user);
        $refreshToken->setToken($refreshTokenString);
        $refreshToken->setExpiresAt((new \DateTimeImmutable())->modify('+7 days'));

        $this->entityManager->persist($refreshToken);
        $this->entityManager->flush();


        $accessTokenCookie = Cookie::create('access_token', $accessToken)
            ->withHttpOnly(true)
            ->withSecure(true)
            ->withExpires((new \DateTime())->modify('+15 min'))
            ->withSameSite('None');


        $refreshTokenCookie = Cookie::create('refresh_token', $refreshTokenString)
            ->withHttpOnly(true)
            ->withSecure(true)
            ->withExpires(
                (new \DateTime())->modify('+7 days')
            )
            ->withPath('/token')
            ->withSameSite('strict');

        $response = new JsonResponse([
            'message' => 'Login successful',
            'userId' => $user->getId(),
            'accessTokenExpiresIn' => 900,
            'refreshTokenExpiresIn' => 604800,
            'xsrfToken' => $xsrfToken,
        ]);

        $response->headers->setCookie($accessTokenCookie);
        $response->headers->setCookie($refreshTokenCookie);

        return $response;
    }
}
