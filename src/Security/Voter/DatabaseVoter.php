<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Database;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DatabaseVoter extends Voter
{
    public const CAN_SHOW_DATABASE = 'can_show_database';

    /**
     * @var string[]
     */
    protected array $attributes = [
        self::CAN_SHOW_DATABASE,
    ];

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, $this->attributes, true)
            && $subject instanceof Database;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        /** @var Database $subject */

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::CAN_SHOW_DATABASE => $subject->getUser()->getId() === $user->getId(),
            default => false,
        };
    }
}
