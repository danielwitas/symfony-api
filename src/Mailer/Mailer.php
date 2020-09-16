<?php

namespace App\Mailer;

use App\Entity\User;
use Twig\Environment;

class Mailer
{
    private $mailer;
    private $twig;

    public function __construct(\Swift_Mailer $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendConfirmationEmail(User $user)
    {
        $body = $this->twig->render('email/confirmation.html.twig', [
            'user' => $user
        ]);
        $message = (new \Swift_Message('Confirm your account!'))
            ->setFrom('hondacivic555@gmail.com')
            ->setTo($user->getEmail())
            ->setBody($body);
        $this->mailer->send($message);
    }

    public function sendPasswordResetEmail(User $user)
    {
        $body = $this->twig->render('email/password-reset.html.twig', [
            'user' => $user
        ]);
        $message = (new \Swift_Message('Password reset!'))
            ->setFrom('hondacivic555@gmail.com')
            ->setTo($user->getEmail())
            ->setBody($body);
        $this->mailer->send($message);
    }

    public function sendNewPassword(User $user, string $password)
    {
        $body = $this->twig->render('email/new-password.html.twig', [
            'user' => $user,
            'password' => $password
        ]);
        $message = (new \Swift_Message('New Password!'))
            ->setFrom('hondacivic555@gmail.com')
            ->setTo($user->getEmail())
            ->setBody($body);
        $this->mailer->send($message);
    }
}