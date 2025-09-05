<?php

namespace App\Controller;

use App\Service\SecurityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
class SecurityController extends AbstractController
{
    public function __construct(
        #[Lazy] private SecurityService $securityService
    ) {}

    #[Route('/login', name: 'app_login', methods: ['POST'])]
    public function login(
        Request $request
        ): Response
    {
        try {
            return $this->securityService->login($request);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }


    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        $response = new JsonResponse(['message' => 'Déconnecté']);
        $response->headers->clearCookie('access_token', '/', null, true, true, 'None');
        $response->headers->clearCookie('refresh_token', '/token', null, true, true, 'None');
        return $response;
    }
}
