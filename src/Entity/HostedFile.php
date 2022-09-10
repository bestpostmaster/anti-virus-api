<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\HostedFileRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=HostedFileRepository::class)
 */
class HostedFile
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
    private string $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @groups("file:read")
     */
    private string $clientName;

    /**
     * @ORM\Column(type="datetime")
     * @groups("file:read")
     */
    private \DateTimeInterface $uploadDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @groups("file:read")
     */
    private ?\DateTimeInterface $expirationDate;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="files")
     * @groups("file:read")
     */
    private UserInterface $user;

    /**
     * @ORM\Column(type="string", length=255)
     * @groups("file:read")
     */
    private string $virtualDirectory;

    /**
     * Size in MB
     *
     * @ORM\Column(type="float")
     * @groups("file:read")
     */
    private float $size;

    /**
     * @ORM\Column(type="boolean")
     * @groups("file:read")
     */
    private $scaned;

    /**
     * @ORM\Column(type="boolean", options={"default" : false})
     * @groups("file:read")
     */
    private bool $infected = false;

    /**
     * @ORM\Column(type="text", length=2000, nullable=true)
     * @groups("file:read")
     */
    private ?string $scanResult;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @groups("file:read")
     */
    private string $description;

    /**
     * @ORM\Column(type="bigint")
     * @groups("file:read")
     */
    private int $downloadCounter;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @groups("file:read")
     */
    private string $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @groups("file:read")
     */
    private string $uploadLocalisation;

    /**
     * @ORM\Column(type="boolean")
     * @groups("file:read")
     */
    private bool $copyrightIssue;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @groups("file:read")
     */
    private string $conversionsAvailable;

    /** @ORM\Column(type="string", length=255, nullable=true) */
    private ?string $filePassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @groups("file:read")
     */
    private array $authorizedUsers;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUploadDate(): \DateTimeInterface
    {
        return $this->uploadDate;
    }

    public function setUploadDate(\DateTimeInterface $uploadDate): self
    {
        $this->uploadDate = $uploadDate;

        return $this;
    }

    public function getUser(): ?int
    {
        return $this->user->getId();
    }

    public function setUser(?UserInterface $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getVirtualDirectory(): ?string
    {
        return $this->virtualDirectory;
    }

    public function setVirtualDirectory(string $virtualDirectory): self
    {
        $this->virtualDirectory = $virtualDirectory;

        return $this;
    }

    public function getSize(): ?float
    {
        return $this->size;
    }

    public function setSize(float $size): self
    {
        $this->size = $size;

        return $this;
    }

    public function getScaned(): ?bool
    {
        return $this->scaned;
    }

    public function setScaned(bool $scaned): self
    {
        $this->scaned = $scaned;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDownloadCounter(): ?int
    {
        return $this->downloadCounter;
    }

    public function setDownloadCounter(int $downloadCounter): self
    {
        $this->downloadCounter = $downloadCounter;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUploadLocalisation(): ?string
    {
        return $this->uploadLocalisation;
    }

    public function setUploadLocalisation(?string $uploadLocalisation): self
    {
        $this->uploadLocalisation = $uploadLocalisation;

        return $this;
    }

    public function getCopyrightIssue(): ?bool
    {
        return $this->copyrightIssue;
    }

    public function setCopyrightIssue(bool $copyrightIssue): self
    {
        $this->copyrightIssue = $copyrightIssue;

        return $this;
    }

    public function getConversionsAvailable(): ?string
    {
        return $this->conversionsAvailable;
    }

    public function setConversionsAvailable(?string $conversionsAvailable): self
    {
        $this->conversionsAvailable = $conversionsAvailable;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function setClientName(string $clientName): void
    {
        $this->clientName = $clientName;
    }

    /**
     * @return ?\DateTimeInterface
     */
    public function getExpirationDate(): ?\DateTimeInterface
    {
        return $this->expirationDate;
    }

    /**
     * @param ?\DateTimeInterface $expirationDate
     */
    public function setExpirationDate(?\DateTimeInterface $expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    public function getFilePassword(): ?string
    {
        return $this->filePassword;
    }

    public function setFilePassword(?string $filePassword): void
    {
        $this->filePassword = $filePassword;
    }

    public function getAuthorizedUsers(): array
    {
        return $this->authorizedUsers;
    }

    public function setAuthorizedUsers(array $authorizedUsers): void
    {
        $this->authorizedUsers = $authorizedUsers;
    }

    public function isInfected(): bool
    {
        return $this->infected;
    }

    public function setInfected(bool $infected): void
    {
        $this->infected = $infected;
    }

    public function getScanResult(): ?string
    {
        return $this->scanResult;
    }

    public function setScanResult(?string $scanResult): void
    {
        $this->scanResult = $scanResult;
    }
}
