<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\FloodRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=FloodRepository::class)
 */
class Flood
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /** @ORM\Column(name="ip", type="string", length=250, nullable=false) */
    private string $ip = '';

    /** @ORM\Column(name="last_try", type="integer", nullable=false) */
    private int $lastTry = 0;

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    public function getLastTry(): int
    {
        return $this->lastTry;
    }

    public function setLastTry(int $lastTry): void
    {
        $this->lastTry = $lastTry;
    }
}
