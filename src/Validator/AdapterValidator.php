<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\AdapterConfig;
use App\Entity\S3Adapter;
use App\Helper\FlysystemHelper;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

final class AdapterValidator extends ConstraintValidator
{
    public function __construct(
        private readonly FlysystemHelper $flysystemHelper,
        private readonly Encryptor $encryptor,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof Adapter) {
            throw new UnexpectedTypeException($constraint, Adapter::class);
        }

        if (null === $value) {
            return;
        }

        if (!$value instanceof AdapterConfig) {
            throw new UnexpectedTypeException($value, AdapterConfig::class);
        }

        // @TODO vérifier que toutes les valeurs qui sont censées être remplies le sont bien
        if ($value instanceof S3Adapter && null !== $value->getS3PlainAccessSecret()) {
            $value->setS3AccessSecret($this->encryptor->encrypt($value->getS3PlainAccessSecret()));
        }

        if (!$this->flysystemHelper->isConnectionOk($value)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ error }}', $this->flysystemHelper->getLastExceptionMessage())
                ->addViolation();
        }
    }
}
