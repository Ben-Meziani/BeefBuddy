<?php

namespace App\Controller;

use App\Service\EmailService;
use App\Service\ResetPasswordService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

// #[Route('/reset-password')]

class ResetPasswordController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private EmailService $emailService,
        private ResetPasswordService $resetPasswordService,
        private LoggerInterface $logger
    ) {}

    #[Route('/reset-password', name: 'app_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            return $this->resetPasswordService->resetPassword($request, $this->getParameter('host_front'));
        }catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur interne.'], 500);
        }
    }

    #[Route('/reset-password-form', name: 'app_reset_password_form', methods: ['POST'])]
    public function resetPasswordForm(
        Request $request
    ): JsonResponse {
        try {
            return $this->resetPasswordService->resetPasswordForm($request);
        } catch (\Exception $e) {
            $this->logger->error('Error resetting password form: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Erreur interne.'], 500);
        }
    }

}
