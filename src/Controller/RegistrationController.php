<?php

namespace App\Controller;

use App\Entity\User;
use App\Security\AppCustomAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

final class RegistrationController extends AbstractController
{
    public function __construct(private EmailVerifier $emailVerifier) {}

    #[Route('/register', name: 'app_registration', methods: ['POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, Security $security): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $user = new User();
            $user->setUsername($data['username']);
            $user->setRoles(['ROLE_USER']);
            $user->setEmail($data['email']);
            // Here you would typically hash the password before saving it
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $data['password']
            );
            $user->setPassword($hashedPassword);
            $user->setIsVerified(false);
            $entityManager->persist($user);

            // actually executes the queries (i.e. the INSERT query)
            $entityManager->flush();

            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('mailer@example.com', 'AcmeMailBot'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            $security->login($user, AppCustomAuthenticator::class, 'main');
            return new JsonResponse(['message' => 'Registration successful. Please check your email to verify your account.'], 200);
        } catch (\Exception $e) {
            $message = $e->getMessage();

            if (str_contains($message, 'UNIQ_IDENTIFIER_USERNAME')) {
                return new JsonResponse(['error' => 'Le username est déjà utilisé.'], 400);
            }
            if (str_contains($message, 'UNIQ_IDENTIFIER_EMAIL')) {
                return new JsonResponse(['error' => 'L\'email est déjà utilisé.'], 400);
            }

            return new JsonResponse(['error' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        EntityManagerInterface $em,
        TranslatorInterface $translator
    ): Response|RedirectResponse {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        // Get the user from the signed URL parameters
        $user = $this->getUser();
        // dd($user);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $user);
            return new RedirectResponse('http://localhost:5173/login');
        } catch (VerifyEmailExceptionInterface $exception) {
            return new JsonResponse(['error' => $translator->trans($exception->getReason(), [], 'VerifyEmailBundle')], 400);
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }
}
