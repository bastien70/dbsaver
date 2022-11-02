<?php

declare(strict_types=1);

namespace App\Validator;

use App\Form\Model\EnableTwoFactorAuthenticationModel;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Totp\TotpAuthenticatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class TotpValidator extends ConstraintValidator
{
    public function __construct(
        private readonly TotpAuthenticatorInterface $totpAuthenticator,
    ) {
    }

    /**
     * @param EnableTwoFactorAuthenticationModel $value
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof Totp) {
            throw new UnexpectedTypeException($constraint, Totp::class);
        }

        if (!$value instanceof EnableTwoFactorAuthenticationModel || null === $value->code) {
            return;
        }

        if (!$this->totpAuthenticator->checkCode($value->user, $value->code)) {
            $this->context->buildViolation($constraint->message)
                ->atPath('code')
                ->addViolation();
        }
    }
}
