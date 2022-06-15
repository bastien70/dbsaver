<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\AdapterConfig;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class AdapterConfigVoter extends Voter
{
    public const CAN_EDIT_OR_REMOVE_ADAPTER = 'can_edit_or_remove_adapter';

    /**
     * @var string[]
     */
    protected array $attributes = [
        self::CAN_EDIT_OR_REMOVE_ADAPTER,
    ];

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return \in_array($attribute, $this->attributes, true)
            && $subject instanceof AdapterConfig;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }

        /* @var AdapterConfig $subject */

        // ... (check conditions and return true to grant permission) ...
        return match ($attribute) {
            self::CAN_EDIT_OR_REMOVE_ADAPTER => $this->canEditOrRemoveAdapter($subject),
            default => false,
        };
    }

    private function canEditOrRemoveAdapter(AdapterConfig $adapterConfig): bool
    {
        foreach ($adapterConfig->getDbases() as $dbase) {
            if ($dbase->getBackups()->count() > 0) {
                return false;
            }
        }

        return true;
    }
}
