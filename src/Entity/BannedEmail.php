<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BannedEmailRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BannedEmailRepository::class)
 */
class BannedEmail
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(name="ip", type="string", length=250, nullable=false) */
    private string $email;

    /** @ORM\Column(name="last_try", type="integer", nullable=false) */
    private int $bannTime;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function getBannTime(): int
    {
        return $this->bannTime;
    }

    public function setBannTime(int $bannTime): void
    {
        $this->bannTime = $bannTime;
    }
}
