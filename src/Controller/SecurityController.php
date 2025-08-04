<?php

namespace App\Controller;

use App\Service\SecurityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class SecurityController extends AbstractController
{
    public function __construct(
        private SecurityService $securityService
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


    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // This method can be blank - it will be intercepted by the logout key on your firewall
    }
}
