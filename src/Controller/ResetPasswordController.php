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
use Symfony\Component\DependencyInjection\Attribute\Lazy;
class ResetPasswordController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        #[Lazy] private EmailService $emailService,
        #[Lazy] private ResetPasswordService $resetPasswordService,
        private LoggerInterface $logger
    ) {}

    #[Route('/reset-password', name: 'app_reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        try {
            return $this->resetPasswordService->resetPassword($request, $this->getParameter('host_front'), $this->getParameter('mail_from'), $this->getParameter('mail_from_name'));
        }catch (\Exception $e) {
            $this->logger->error('Error in resetPasswordController: ' . $e->getMessage());
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
            $this->logger->error('Error in resetPasswordFormController: ' . $e->getMessage());
            return new JsonResponse(['error' => 'Erreur interne.'], 500);
        }
    }

}
