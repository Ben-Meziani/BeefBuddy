<?php

namespace App\Service;

use App\Entity\User;
use App\Security\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class EmailService
{
    public function __construct(
        private MailerInterface $mailer,
        private EmailVerifier $emailVerifier,
        private ParameterBagInterface $params
    )
    {

    }

    public function sendEmail(
        User $user,
        string $subject,
        string $template,
        array $context,
    ): void
    {
        $this->emailVerifier->sendEmailConfirmation(
            'app_verify_email',
            $user,
            (new TemplatedEmail())
                ->from(new Address($this->params->get('mail_from'), $this->params->get('mail_from_name')))
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
