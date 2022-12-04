<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AntiSpamTokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AntiSpamTokenRepository::class)
 */
class ContactMessage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(type="string", length=250, nullable=false) */
    private string $senderEmail = '';

    /** @ORM\Column(type="text", length=20000, nullable=true) */
    private ?string $message;

    /** @ORM\Column(type="datetime") */
    private \DateTimeInterface $sendingDate;

    /** @ORM\Column(type="string", length=250, nullable=false) */
    private string $senderIp = '';

    public function getId()
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    public function setSenderEmail(string $senderEmail): void
    {
        $this->senderEmail = $senderEmail;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(?string $message): void
    {
        $this->message = $message;
    }

    public function getSendingDate(): \DateTimeInterface
    {
        return $this->sendingDate;
    }

    public function setSendingDate(\DateTimeInterface $sendingDate): void
    {
        $this->sendingDate = $sendingDate;
    }

    public function getSenderIp(): string
    {
        return $this->senderIp;
    }

    public function setSenderIp(string $senderIp): void
    {
        $this->senderIp = $senderIp;
    }
}
