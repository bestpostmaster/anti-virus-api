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

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=250, nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $ip = '';

    /**
     * @var int
     *
     * @ORM\Column(name="last_try", type="integer", nullable=false)
     */
    private $lastTry = '0';

    public function getIp(): string
    {
        return $this->ip;
    }

    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return int
     */
    public function getLastTry()
    {
        return $this->lastTry;
    }

    /**
     * @param int $lastTry
     */
    public function setLastTry($lastTry): void
    {
        $this->lastTry = $lastTry;
    }
}
