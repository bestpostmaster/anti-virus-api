<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    protected const RESPONSE_ONE = '19';
    protected const RESPONSE_TWO = '17';
    private MailerInterface $mailer;
    private string $webSiteProtocol;
    private string $webSiteName;
    private string $webSiteDomainName;
    private string $webSiteHomeUrl;
    private string $webSiteEmailAddress;

    public function __construct(MailerInterface $mailer, string $webSiteProtocol, string $webSiteName, string $webSiteDomainName, string $webSiteHomeUrl, string $webSiteEmailAddress)
    {
        $this->mailer = $mailer;
        $this->webSiteProtocol = $webSiteProtocol;
        $this->webSiteName = $webSiteName;
        $this->webSiteDomainName = $webSiteDomainName;
        $this->webSiteHomeUrl = $webSiteHomeUrl;
        $this->webSiteEmailAddress = $webSiteEmailAddress;
    }

    /**
     * @Route(
     *     "/{_locale}/contact",
     *     name="contact",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi",
     *     }
     * )
     */
    public function index(Request $request): Response
    {
        return $this->render('app/contact.html.twig', [
            'lang' => $request->get('_locale'),
        ]);
    }

    /**
     * @Route(
     *     "/{_locale}/send-contact-message",
     *     name="send-contact-message",
     *     requirements={
     *         "_locale": "en|fr|de|es|zh|ar|hi",
     *     }
     * )
     */
    public function sendContactMessage(Request $request): Response
    {
        $data = (array) json_decode($request->getContent());

        if (!isset($data['email']) || !isset($data['message']) || !isset($data['response1']) || !isset($data['response2'])
            || $data['response1'] !== $this::RESPONSE_ONE || $data['response2'] !== $this::RESPONSE_TWO) {
            return $this->json($data, 400, [], []);
        }

        $email = (new Email())
            ->from($this->webSiteEmailAddress)
            ->to($this->webSiteEmailAddress)
            // ->cc('cc@example.com')
            // ->bcc('bcc@example.com')
            // ->replyTo('fabien@example.com')
            // ->priority(Email::PRIORITY_HIGH)
            ->subject('Message to admin')
            ->text($data['message'])
            ->html($data['message']);

        $this->mailer->send($email);

        return $this->json(['confirmation' => 'ok'], 200, [], []);
    }
}
