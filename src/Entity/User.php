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
    private $id;

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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isBanned = false;

    /**
     * @ORM\OneToMany(targetEntity=HostedFile::class, mappedBy="user")
     */
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

    public function getUserIdentifier(): string
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = strtolower($email);
    }

    /**
     * @return string
     */
    public function getPhoneNumber(): string
    {
        return $this->phoneNumber;
    }

    /**
     * @param string $phoneNumber
     */
    public function setPhoneNumber(string $phoneNumber): void
    {
        $this->phoneNumber = $phoneNumber;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @param string $city
     */
    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    /**
     * @return string
     */
    public function getZipCode(): string
    {
        return $this->zipCode;
    }

    /**
     * @param string $zipCode
     */
    public function setZipCode(string $zipCode): void
    {
        $this->zipCode = $zipCode;
    }

    /**
     * @return string
     */
    public function getPreferredLanguage(): string
    {
        return $this->preferredLanguage;
    }

    /**
     * @param string $preferredLanguage
     */
    public function setPreferredLanguage(string $preferredLanguage): void
    {
        $this->preferredLanguage = $preferredLanguage;
    }

    /**
     * @return string
     */
    public function getTypeOfAccount(): string
    {
        return $this->typeOfAccount;
    }

    /**
     * @param string $typeOfAccount
     */
    public function setTypeOfAccount(string $typeOfAccount): void
    {
        $this->typeOfAccount = $typeOfAccount;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getAvatarPicture(): string
    {
        return $this->avatarPicture;
    }

    /**
     * @param string $avatarPicture
     */
    public function setAvatarPicture(string $avatarPicture): void
    {
        $this->avatarPicture = $avatarPicture;
    }

    /**
     * @return string
     */
    public function getDateOfBirth(): string
    {
        return $this->dateOfBirth;
    }

    /**
     * @param string $dateOfBirth
     */
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

    /**
     * @return \DateTimeInterface
     */
    public function getRegistrationDate(): \DateTimeInterface
    {
        return $this->registrationDate;
    }

    /**
     * @param \DateTimeInterface $registrationDate
     */
    public function setRegistrationDate(\DateTimeInterface $registrationDate): void
    {
        $this->registrationDate = $registrationDate;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getLastConnexionDate(): \DateTimeInterface
    {
        return $this->lastConnexionDate;
    }

    /**
     * @param \DateTimeInterface $lastConnexionDate
     */
    public function setLastConnexionDate(\DateTimeInterface $lastConnexionDate): void
    {
        $this->lastConnexionDate = $lastConnexionDate;
    }

    /**
     * @return string
     */
    public function getSecretTokenForValidation(): string
    {
        return $this->secretTokenForValidation;
    }

    /**
     * @param string $secretTokenForValidation
     */
    public function setSecretTokenForValidation(string $secretTokenForValidation): void
    {
        $this->secretTokenForValidation = $secretTokenForValidation;
    }

    private \DateTimeInterface $lastConnexionDate;

    private string $secretTokenForValidation;

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

    public function getId(): ?int
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
}
