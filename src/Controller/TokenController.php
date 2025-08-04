<?php

namespace App\Controller;

use App\Service\TokenService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TokenController extends AbstractController
{
    public function __construct(
        private TokenService $tokenService
    ) {}

    #[Route('/token/refresh', name: 'refresh_token', methods: ['GET'])]
    public function refreshToken(
        Request $request
        ): JsonResponse
    {
        try {
            return $this->tokenService->refreshToken($request);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
