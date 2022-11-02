<?php

declare(strict_types=1);

namespace App\Validator;

use Scheb\TwoFactorBundle\Model\Totp\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class TotpValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TotpAuthenticatorInterface $totpAuthenticator,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }

    /**
     * @param string|null $value
     */
    public function validate($value, Constraint $constraint): void
    {
        \assert($constraint instanceof Totp);
        if (null === $value || '' === $value) {
            return;
        }

        $user = $this->tokenStorage->getToken()?->getUser();
        \assert($user instanceof TwoFactorInterface);

        if (!$this->totpAuthenticator->checkCode($user, $value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $value)
                ->addViolation();
        }
    }
}
