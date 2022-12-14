<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @groups("user:read", "file:read")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @groups("user:read", "file:read")
     */
    private $login;

    /**
     * @ORM\Column(type="json")
     * @groups("user:read", "file:read")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     *
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @groups("user:read", "file:read")
     */
    private $email;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @groups("user:read", "file:read")
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @groups("user:read", "file:read")
     */
    private $city;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @groups("user:read", "file:read")
     */
    private $country;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @groups("user:read", "file:read")
     */
    private $zipCode;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @groups("user:read", "file:read")
     */
    private $ip;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @groups("user:read", "file:read")
     */
    private $preferredLanguage;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @groups("user:read", "file:read")
     */
    private $typeOfAccount;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @groups("user:read", "file:read")
     */
    private $description;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @groups("user:read", "file:read")
     */
    private string $avatarPicture;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @groups("user:read", "file:read")
     */
    private $dateOfBirth;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @groups("user:read", "file:read")
     */
    private \DateTimeInterface $passwordDate;

    /** @ORM\Column(type="boolean", nullable=true) */
    private $isBanned = false;

    /** @ORM\Column(type="boolean", nullable=true) */
    private $deleteAccountRequested = false;

    /** @ORM\Column(type="boolean", nullable=true) */
    private $newPasswordRequested = false;

    /** @ORM\Column(type="boolean", nullable=true) */
    private $deleteAccountConfirmed = false;

    /** @ORM\Column(type="boolean", nullable=false)
     * @groups("user:read", "file:read")
     */
    private bool $sendEmailAfterEachAction = false;

    /** @ORM\Column(type="boolean", nullable=false)
     * @groups("user:read", "file:read")
     */
    private bool $sendEmailIfFileIsInfected = false;

    /** @ORM\Column(type="boolean", nullable=false)
     * @groups("user:read", "file:read")
     */
    private bool $sendSmsIfFileIsInfected = false;

    /** @ORM\Column(type="string", nullable=true)
     * @groups("user:read", "file:read")
     */
    private string $postUrlAfterAction;

    /** @ORM\Column(type="boolean", nullable=true)
     * @groups("user:read", "file:read")
     */
    private bool $sendPostToUrlAfterEachAction = false;

    /** @ORM\Column(type="boolean", nullable=true)
     * @groups("user:read", "file:read")
     */
    private bool $sendPostToUrlIfFileIsInfected = false;

    /** @ORM\Column(type="boolean", nullable=true, options={"default" : false}) */
    private bool $emailConfirmed = false;

    /** @ORM\OneToMany(targetEntity=HostedFile::class, mappedBy="user") */
    private $files;

    /**
     * @ORM\Column(type="float", length=60, nullable=true)
     * @groups("user:read", "file:read")
     */
    private float $totalSpaceUsedMo = 0;

    /**
     * @ORM\Column(type="float", nullable=true, options={"default" : 100.0000})
     * @groups("user:read", "file:read")
     */
    private float $authorizedSizeMo = 100.0000;

    /**
     * @ORM\Column(type="datetime", length=60, nullable=true)
     * @groups("user:read", "file:read")
     */
    private \DateTimeInterface $registrationDate;

    /**
     * @ORM\Column(type="datetime", length=60, nullable=true)
     * @groups("user:read", "file:read")
     */
    private \DateTimeInterface $lastConnexionDate;

    /** @ORM\Column(type="string", nullable=true) */
    private string $secretTokenForValidation;

    /** @ORM\OneToMany(targetEntity=ActionRequested::class, mappedBy="user") */
    private $actionsRequested;

    public function getUserIdentifier(): string
    {
        return $this->login;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = strtolower($email);
    }

    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    public function getPreferredLanguage(): string
    {
        return $this->preferredLanguage;
    }

    public function setPreferredLanguage(string $preferredLanguage): void
    {
        $this->preferredLanguage = $preferredLanguage;
    }

    public function getTypeOfAccount(): string
    {
        return $this->typeOfAccount;
    }

    public function setTypeOfAccount(string $typeOfAccount): void
    {
        $this->typeOfAccount = $typeOfAccount;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getAvatarPicture(): string
    {
        return $this->avatarPicture;
    }

    public function setAvatarPicture(string $avatarPicture): void
    {
        $this->avatarPicture = $avatarPicture;
    }

    public function getDateOfBirth(): string
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(string $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return string
     */
    public function getIsBanned()
    {
        return $this->isBanned;
    }

    /**
     * @param string $isBanned
     */
    public function setIsBanned($isBanned): void
    {
        $this->isBanned = $isBanned;
    }

    public function getRegistrationDate(): \DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    public function getLastConnexionDate(): \DateTimeInterface
    {
        return $this->lastConnexionDate;
    }

    public function setLastConnexionDate(\DateTimeInterface $lastConnexionDate): void
    {
        $this->lastConnexionDate = $lastConnexionDate;
    }

    public function getSecretTokenForValidation(): string
    {
        return $this->secretTokenForValidation;
    }

    public function setSecretTokenForValidation(string $secretTokenForValidation): void
    {
        $this->secretTokenForValidation = $secretTokenForValidation;
    }

    public function getTotalSpaceUsedMo(): float
    {
        return $this->totalSpaceUsedMo;
    }

    public function setTotalSpaceUsedMo(float $totalSpaceUsedMo): self
    {
        $this->totalSpaceUsedMo = $totalSpaceUsedMo;

        return $this;
    }

    public function getAuthorizedSizeMo(): ?float
    {
        return $this->authorizedSizeMo;
    }

    public function setAuthorizedSizeMo(float $authorizedSizeMo): self
    {
        $this->authorizedSizeMo = $authorizedSizeMo;

        return $this;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function setFiles($files): self
    {
        $this->files = $files;

        return $this;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getLogin(): ?string
    {
        return $this->login;
    }

    public function setLogin(string $login): self
    {
        $this->login = strtolower($login);

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->login;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isEmailConfirmed(): bool
    {
        return $this->emailConfirmed;
    }

    public function setEmailConfirmed(bool $emailConfirmed): void
    {
        $this->emailConfirmed = $emailConfirmed;
    }

    public function getActionsRequested()
    {
        return $this->actionsRequested;
    }

    public function setActionsRequested($actionsRequested): void
    {
        $this->actionsRequested = $actionsRequested;
    }

    public function isSendEmailAfterEachAction(): bool
    {
        return $this->sendEmailAfterEachAction;
    }

    public function setSendEmailAfterEachAction(bool $sendEmailAfterEachAction): void
    {
        $this->sendEmailAfterEachAction = $sendEmailAfterEachAction;
    }

    public function isSendEmailIfFileIsInfected(): bool
    {
        return $this->sendEmailIfFileIsInfected;
    }

    public function setSendEmailIfFileIsInfected(bool $sendEmailIfFileIsInfected): void
    {
        $this->sendEmailIfFileIsInfected = $sendEmailIfFileIsInfected;
    }

    public function isSendSmsIfFileIsInfected(): bool
    {
        return $this->sendSmsIfFileIsInfected;
    }

    public function setSendSmsIfFileIsInfected(bool $sendSmsIfFileIsInfected): void
    {
        $this->sendSmsIfFileIsInfected = $sendSmsIfFileIsInfected;
    }

    public function getPostUrlAfterAction(): string
    {
        return $this->postUrlAfterAction;
    }

    public function setPostUrlAfterAction(string $postUrlAfterAction): void
    {
        $this->postUrlAfterAction = $postUrlAfterAction;
    }

    public function isSendPostToUrlAfterEachAction(): bool
    {
        return $this->sendPostToUrlAfterEachAction;
    }

    public function setSendPostToUrlAfterEachAction(bool $sendPostToUrlAfterEachAction): void
    {
        $this->sendPostToUrlAfterEachAction = $sendPostToUrlAfterEachAction;
    }

    public function isSendPostToUrlIfFileIsInfected(): bool
    {
        return $this->sendPostToUrlIfFileIsInfected;
    }

    public function setSendPostToUrlIfFileIsInfected(bool $sendPostToUrlIfFileIsInfected): void
    {
        $this->sendPostToUrlIfFileIsInfected = $sendPostToUrlIfFileIsInfected;
    }

    public function getPasswordDate(): \DateTimeInterface
    {
        return $this->passwordDate;
    }

    public function setPasswordDate(\DateTimeInterface $passwordDate): void
    {
        $this->passwordDate = $passwordDate;
    }

    public function isDeleteAccountRequested(): bool
    {
        return $this->deleteAccountRequested;
    }

    public function setDeleteAccountRequested(bool $deleteAccountRequested): void
    {
        $this->deleteAccountRequested = $deleteAccountRequested;
    }

    public function isDeleteAccountConfirmed(): bool
    {
        return $this->deleteAccountConfirmed;
    }

    public function setDeleteAccountConfirmed(bool $deleteAccountConfirmed): void
    {
        $this->deleteAccountConfirmed = $deleteAccountConfirmed;
    }

    public function getIp()
    {
        return $this->ip;
    }

    public function setIp($ip): void
    {
        $this->ip = $ip;
    }

    public function isNewPasswordRequested(): bool
    {
        return $this->newPasswordRequested;
    }

    public function setNewPasswordRequested(bool $newPasswordRequested): void
    {
        $this->newPasswordRequested = $newPasswordRequested;
    }
}
