<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const EDIT = 'USER_EDIT';
    public const VIEW_ONE = 'USER_VIEW_ONE';
    public const VIEW_ALL = 'USER_VIEW_ALL';

    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::EDIT, self::VIEW_ONE, self::VIEW_ALL])) {
            return false;
        }

        if (is_array($subject) && !empty($subject)) {
            foreach ($subject as $item) {
                if (!($item instanceof User)) {
                    return false;
                }
            }

            return true;
        }

        return $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $connectedUser = $token->getUser();

        if (!$connectedUser instanceof UserInterface) {
            return false;
        }

        if ($this->security->isGranted('ROLE_ADMIN')) {
            return true;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $connectedUser);
            case self::VIEW_ONE:
                return $this->canViewOne($subject, $connectedUser);
            case self::VIEW_ALL:
                return $this->canViewAll();
        }

        return false;
    }

    private function canEdit(UserInterface $user, UserInterface $connectedUser): bool
    {
        return $user->getUserIdentifier() === $connectedUser->getUserIdentifier();
    }

    private function canViewAll(): bool
    {
        return false;
    }

    private function canViewOne(UserInterface $user, UserInterface $connectedUser): bool
    {
        return $user->getUserIdentifier() === $connectedUser->getUserIdentifier();
    }
}
