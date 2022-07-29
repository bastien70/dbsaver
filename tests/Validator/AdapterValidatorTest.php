<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Entity\Enum\S3Provider;
use App\Entity\LocalAdapter;
use App\Entity\S3Adapter;
use App\Helper\FlysystemHelper;
use App\Validator\Adapter as AdapterConstraint;
use App\Validator\AdapterValidator;
use App\Validator\Database as DatabaseConstraint;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class AdapterValidatorTest extends TestCase
{
    private ExecutionContext|MockObject|null $context;
    private ?ConstraintViolationBuilderInterface $constraintViolationBuilder;
    private ?AdapterValidator $validator;

    protected function setUp(): void
    {
        $this->context = $this->getMockBuilder(ExecutionContext::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->constraintViolationBuilder = $this->getMockBuilder(ConstraintViolationBuilderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->context
            ->method('buildViolation')
            ->willReturn($this->constraintViolationBuilder);
        $this->constraintViolationBuilder
            ->method('setParameter')
            ->willReturn($this->constraintViolationBuilder);
        $encryptor = new Encryptor('test', 'aes-256-ctr', false, false, false);
        $encryptor->setSecretIv('test');
        $helper = new FlysystemHelper($encryptor, 'path/to/project');
        $this->validator = new AdapterValidator($helper, $encryptor);
        $this->validator->initialize($this->context);
    }

    protected function tearDown(): void
    {
        $this->context = null;
        $this->constraintViolationBuilder = null;
        $this->validator = null;
    }

    public function testNullIsValid(): void
    {
        $this->context->expects($this->never())
            ->method('addViolation');

        $this->validator->validate(null, new AdapterConstraint());
    }

    public function testValidEntity(): void
    {
        $this->context->expects($this->never())
            ->method('addViolation');

        $localAdapter = (new LocalAdapter())
            ->setName('local')
            ->setPrefix('backups');

        $this->validator->validate($localAdapter, new AdapterConstraint());

        $s3Adapter = (new S3Adapter())
            ->setName('minio')
            ->setPrefix('backups')
            ->setS3BucketName('somebucketname')
            ->setS3AccessId('minio')
            ->setS3PlainAccessSecret('minio123')
            ->setS3Provider(S3Provider::OTHER)
            ->setS3Endpoint('http://127.0.0.1:9004')
            ->setS3Region('eu-east-1');

        $this->validator->validate($s3Adapter, new AdapterConstraint());
    }

    public function testInvalidConfig(): void
    {
        $constraint = new AdapterConstraint();

        $s3Adapter = (new S3Adapter())
            ->setName('minio')
            ->setPrefix('backups')
            ->setS3BucketName('somebucketname')
            ->setS3AccessId('minio')
            ->setS3PlainAccessSecret('bad_access_secret')
            ->setS3Provider(S3Provider::OTHER)
            ->setS3Endpoint('http://127.0.0.1:9004')
            ->setS3Region('eu-east-1');

        $this->context->expects($this->once())
            ->method('buildViolation');

        $this->validator->validate($s3Adapter, $constraint);
    }

    public function testInvalidConstraintType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate(null, new NotBlank());
    }

    public function testInvalidValueType(): void
    {
        $this->expectException(UnexpectedTypeException::class);
        $this->validator->validate('string', new DatabaseConstraint());
    }
}
