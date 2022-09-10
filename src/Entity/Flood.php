<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FloodRepository;

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

    /**
     * @return string
     */
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
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
