<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ActionRepository::class)
 */
class Action
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=250, nullable=false)
     * @groups("file:read")
     */
    private string $actionName;

    /** @ORM\Column(type="string", length=250, nullable=false) */
    private string $commandToRun;

    /** @ORM\Column(type="string", length=250, nullable=false) */
    private string $description;

    /** @ORM\Column(type="string", length=250, nullable=false) */
    private string $provider;

    /** @ORM\Column(type="string", length=250, nullable=false) */
    private string $type;

    /** @ORM\Column(type="boolean", nullable=false) */
    private $enabled;

    /** @ORM\Column(type="boolean", nullable=false) */
    private bool $hidden;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getCommandToRun(): string
    {
        return $this->commandToRun;
    }

    public function setCommandToRun(string $commandToRun): void
    {
        $this->commandToRun = $commandToRun;
    }

    public function getEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function isHidden(): bool
    {
        return $this->hidden;
    }

    public function setHidden($hidden): void
    {
        $this->hidden = $hidden;
    }

    public function getActionName(): string
    {
        return $this->actionName;
    }

    public function setActionName(string $actionName): void
    {
        $this->actionName = $actionName;
    }
}
