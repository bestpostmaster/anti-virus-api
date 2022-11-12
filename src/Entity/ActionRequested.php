<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ActionRequestedRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
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

    /** @ORM\Column(type="text", length=20000, nullable=true) */
    private ?string $actionParameters;

    /**
     * @ORM\Column(type="datetime")
     * @groups("file:read")
     */
    private \DateTimeInterface $dateOfDemand;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @groups("file:read")
     */
    private ?\DateTimeInterface $startTime;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @groups("file:read")
     */
    private ?\DateTimeInterface $endTime;

    /** @ORM\Column(type="json", nullable=false)
     * @groups("file:read")
     */
    private array $hostedFileIds;

    /** @ORM\ManyToOne(targetEntity=Action::class, inversedBy="actionsRequested")
     * @groups("file:read")
     */
    private Action $action;

    /** @ORM\ManyToOne(targetEntity=User::class, inversedBy="actionsRequested")
     * @groups("file:read")
     */
    private UserInterface $user;

    /** @ORM\Column(type="boolean", nullable=false)
     * @groups("file:read")
     */
    private bool $accomplished = false;

    /** @ORM\Column(type="string", length=45, nullable=false)
     * @groups("file:read")
     */
    private string $userIsNotifiedByEmail = 'off'; // Possibles values 'ok', 'ko', 'off'

    /** @ORM\Column(type="string", length=45, nullable=false)
     * @groups("file:read")
     */
    private string $userIsNotifiedByPostQuery = 'off'; // Possibles values 'ok', 'ko', 'off'

    /** @ORM\Column(type="json", nullable=false)
     * @groups("file:read")
     */
    private array $actionResults = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getActionParameters(): ?string
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

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->startTime;
    }

    public function setStartTime(\DateTimeInterface $startTime): void
    {
        $this->startTime = $startTime;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->endTime;
    }

    public function setEndTime(\DateTimeInterface $endTime): void
    {
        $this->endTime = $endTime;
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

    public function getAction(): Action
    {
        return $this->action;
    }

    public function setAction(Action $action): void
    {
        $this->action = $action;
    }

    public function getHostedFileIds(): array
    {
        return $this->hostedFileIds;
    }

    public function setHostedFileIds(array $hostedFileIds): void
    {
        $this->hostedFileIds = $hostedFileIds;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    public function isUserIsNotifiedByEmail(): string
    {
        return $this->userIsNotifiedByEmail;
    }

    public function setUserIsNotifiedByEmail(string $userIsNotifiedByEmail): void
    {
        $this->userIsNotifiedByEmail = $userIsNotifiedByEmail;
    }

    public function isUserIsNotifiedByPostQuery(): string
    {
        return $this->userIsNotifiedByPostQuery;
    }

    public function setUserIsNotifiedByPostQuery(string $userIsNotifiedByPostQuery): void
    {
        $this->userIsNotifiedByPostQuery = $userIsNotifiedByPostQuery;
    }
}
