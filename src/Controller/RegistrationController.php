<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\EmailService;
use App\Service\UserRegistrationService;
use Symfony\Component\DependencyInjection\Attribute\Lazy;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

final class RegistrationController extends AbstractController
{
    public function __construct(
        private EmailService $emailService,
        private UserRegistrationService $registrationService
    ) {}

    #[Route('/register', name: 'app_registration', methods: ['POST'])]
    public function register(Request $request): JsonResponse
    {
        try {
            $this->registrationService->register($request, $this->getParameter('host_front'), $this->getParameter('mail_from'), $this->getParameter('mail_from_name'));
            return new JsonResponse(['message' => 'Inscription réussie. Vérifiez votre email.'], 200);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Erreur interne.'], 500);
        }
    }

    //TODO: Remove if not needed
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ): Response|RedirectResponse {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // Get the user from the signed URL parameters
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailService->handleEmailConfirmation($request, $user);
            return new RedirectResponse($this->getParameter('host_front') . '/login');
        } catch (VerifyEmailExceptionInterface $exception) {
            return new JsonResponse(['error' => $translator->trans($exception->getReason(), [], 'VerifyEmailBundle')], 400);
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
