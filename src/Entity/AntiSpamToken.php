<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\AntiSpamTokenRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AntiSpamTokenRepository::class)
 */
class AntiSpamToken
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(name="token", type="string", length=250, nullable=false, unique=true) */
    private string $token;

    /** @ORM\Column(type="datetime") */
    private \DateTimeInterface $creationDate;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
        $this->token = $token;
    }

    public function getCreationDate(): \DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): void
    {
        $this->creationDate = $creationDate;
    }
}
