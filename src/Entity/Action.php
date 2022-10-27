<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ActionRepository;
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
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=250, nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @groups("file:read")
     */
    private $actionName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=250, nullable=false)
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $commandToRun;

    /** @ORM\Column(type="boolean", nullable=false) */
    private $enabled;

    /** @ORM\OneToMany(targetEntity=ActionRequested::class, mappedBy="action") */
    private $actionsRequested;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getActionName(): string
    {
        return $this->actionName;
    }

    public function setActionName(string $actionName): void
    {
        $this->actionName = $actionName;
    }

    public function getCommandToRun(): string
    {
        return $this->commandToRun;
    }

    public function setCommandToRun(string $commandToRun): void
    {
        $this->commandToRun = $commandToRun;
    }

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    public function getActionsRequested()
    {
        return $this->actionsRequested;
    }

    public function setActionsRequested(ActionRequested $actionsRequested): void
    {
        $this->actionsRequested = $actionsRequested;
    }
}
