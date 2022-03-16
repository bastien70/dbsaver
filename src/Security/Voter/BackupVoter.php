<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Backup;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class BackupVoter extends Voter
{
    public const CAN_SHOW_BACKUP = 'can_show_backup';

    /**
     * @var string[]
     */
    protected array $attributes = [
        self::CAN_SHOW_BACKUP,
    ];

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return \in_array($attribute, $this->attributes, true)
            && $subject instanceof Backup;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        /* @var Backup $subject */

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::CAN_SHOW_BACKUP => $subject->getDatabase()->getOwner()->getId() === $user->getId(),
            default => false,
        };
    }
}
