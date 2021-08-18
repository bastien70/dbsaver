<?php

namespace App\Security\Voter;

use App\Entity\Backup;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class BackupVoter extends Voter
{
    public const CAN_SHOW_BACKUP = 'can_show_backup';

    protected array $attributes = [
        self::CAN_SHOW_BACKUP,
    ];

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, $this->attributes, true)
            && $subject instanceof Backup;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /** @var Backup $subject */

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::CAN_SHOW_BACKUP => $subject->getDb()->getUser()->getId() === $user->getId(),
            default => false,
        };

    }
}
