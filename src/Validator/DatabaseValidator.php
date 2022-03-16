<?php

declare(strict_types=1);

namespace App\Validator;

use App\Helper\DatabaseHelper;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class DatabaseValidator extends ConstraintValidator
{
    public function __construct(private DatabaseHelper $databaseHelper, private Encryptor $encryptor)
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Database) {
            throw new UnexpectedTypeException($constraint, Database::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof \App\Entity\Database) {
            throw new UnexpectedTypeException($value, \App\Entity\Database::class);
        }

        if (null === $value->getHost() || null === $value->getUser() || null === $value->getName() || (null === $value->getPlainPassword() && null === $value->getPassword())) {
            return;
        }

        if (null !== $value->getPlainPassword()) {
            $value->setPassword($this->encryptor->encrypt($value->getPlainPassword()));
        }

        if (!$this->databaseHelper->isConnectionOk($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ error }}', $this->databaseHelper->getLastExceptionMessage())
                ->addViolation();
        }
    }
}
