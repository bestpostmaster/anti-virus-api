<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ActionRequestedRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ActionRequestedRepository::class)
 */
class ActionRequested
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @groups("file:read")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @groups("file:read")
     */
    private string $actionName;

    /**
     * @ORM\Column(type="string", length=255)
     * @groups("file:read")
     */
    private string $actionParameters;

    /**
     * @ORM\Column(type="datetime")
     * @groups("file:read")
     */
    private \DateTimeInterface $dateOfDemand;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @groups("file:read")
     */
    private \DateTimeInterface $startTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @groups("file:read")
     */
    private \DateTimeInterface $endTime;

    /**
     * @ORM\ManyToOne(targetEntity=HostedFile::class, inversedBy="actionsRequested")
     * @groups("file:read")
     */
    private HostedFile $hostedFile;

    /** @ORM\Column(type="boolean", nullable=false) */
    private bool $accomplished = false;

    /** @ORM\Column(type="json", nullable=false) */
    private array $actionResults = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
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

    public function getActionParameters(): string
    {
        return $this->actionParameters;
    }

    public function setActionParameters(string $actionParameters): void
    {
        $this->actionParameters = $actionParameters;
    }

    public function getDateOfDemand(): \DateTimeInterface
    {
        return $this->dateOfDemand;
    }

    public function setDateOfDemand(\DateTimeInterface $dateOfDemand): void
    {
        $this->dateOfDemand = $dateOfDemand;
    }

    public function getStartTime(): \DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): \DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): void
    {
        $this->endTime = $endTime;
    }

    public function getHostedFile(): ?int
    {
        return $this->hostedFile->getId();
    }

    public function setHostedFile(HostedFile $hostedFile): void
    {
        $this->hostedFile = $hostedFile;
    }

    public function isAccomplished(): bool
    {
        return $this->accomplished;
    }

    public function setAccomplished(bool $accomplished): void
    {
        $this->accomplished = $accomplished;
    }

    public function getActionResults(): array
    {
        return $this->actionResults;
    }

    public function setActionResults(array $actionResults): void
    {
        $this->actionResults = $actionResults;
    }
}
