<?php

namespace App\Service;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private EmailVerifier $emailVerifier
    )
    {

    }

    public function sendEmail(User $user, string $url, string $subject, string $template, array $context)
    {
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address('mailer@example.com', 'AcmeMailBot'))
                ->to($user->getEmail())
                ->subject($subject)
                ->htmlTemplate($template)
                ->context($context)
        );
    }

    public function handleEmailConfirmation(Request $request, User $user)
    {
        $this->emailVerifier->handleEmailConfirmation($request, $user);
    }
}
