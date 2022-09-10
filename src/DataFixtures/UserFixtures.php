<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\HostedFile;
use App\Entity\User;
use DateTimeZone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordEncoder;
    private string $adminPassword;
    private string $defaultUserPassword;

    public function __construct(UserPasswordHasherInterface $passwordEncoder, string $adminPassword, string $defaultUserPassword)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->adminPassword = $adminPassword;
        $this->defaultUserPassword = $defaultUserPassword;
    }

    public function load(ObjectManager $manager): void
    {
        $users = [
            [
                'login' => 'admin',
                'roles' => ['ROLE_ADMIN'],
                'pass' => $this->adminPassword
            ],
            [
                'login' => 'user',
                'roles' => ['ROLE_USER'],
                'pass' => $this->defaultUserPassword
            ]
        ];

        foreach ($users as $item) {
            $user = new User();
            $user->setLogin($item['login']);
            $user->setRoles($item['roles']);
            $user->setPassword($this->passwordEncoder->hashPassword(
                $user,
                $item['pass']
            ));

            $manager->persist($user);
            $manager->flush($user);
        }
    }
}
