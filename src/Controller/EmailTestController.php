<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class EmailTestController extends AbstractController
{
    /**
     * @Route("/test-email", name="test-email")
     *
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function sendEmail(MailerInterface $mailer): Response
    {
        $link = 'https://www.google.com/';

        $email = (new TemplatedEmail())
            ->from('**************@gmail.com')
            ->to('***************@gmail.com')
            // ->cc('cc@example.com')
            // ->bcc('bcc@example.com')
            // ->replyTo('fabien@example.com')
            // ->priority(Email::PRIORITY_HIGH)
            ->subject('Activate your account')
            ->htmlTemplate('app/mails/confirm-email.html.twig')
            ->context([
                'link' => $link,
            ]);

        $mailer->send($email);

        return new Response('ok');
    }
}
