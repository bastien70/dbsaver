<?php

declare(strict_types=1);

namespace App\Tests\Validator;

use App\Entity\Database as DatabaseEntity;
use App\Helper\DatabaseHelper;
use App\Validator\Database as DatabaseConstraint;
use App\Validator\DatabaseValidator;
use Nzo\UrlEncryptorBundle\Encryptor\Encryptor;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Context\ExecutionContext;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

final class DatabaseValidatorTest extends TestCase
{
    private ExecutionContext|MockObject|null $context;
    private ?ConstraintViolationBuilderInterface $constraintViolationBuilder;
    private ?DatabaseValidator $validator;

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
        $helper = new DatabaseHelper($encryptor);
        $this->validator = new DatabaseValidator($helper, $encryptor);
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

        $this->validator->validate(null, new DatabaseConstraint());
    }

    public function testValidEntity(): void
    {
        $this->context->expects($this->never())
            ->method('addViolation');

        $entity = (new DatabaseEntity())
            ->setHost('127.0.0.1')
            ->setUser('root')
            ->setPlainPassword('root')
            ->setPort(3307)
            ->setName('dbsaver_test');

        $this->validator->validate($entity, new DatabaseConstraint());
    }

    public function testInvalidConfig(): void
    {
        $constraint = new DatabaseConstraint();

        $entity = (new DatabaseEntity())
            ->setHost('127.0.0.1')
            ->setUser('test')
            ->setPlainPassword('unknown')
            ->setName('invalid_database');

        $this->context->expects($this->once())
            ->method('buildViolation');

        $this->validator->validate($entity, $constraint);
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

    /**
     * @dataProvider provideEmptyValidCases
     */
    public function testEmptyIsValid(?string $host, ?string $user, ?string $plainPassword, ?string $password, ?string $name): void
    {
        $this->context->expects($this->never())
            ->method('addViolation');

        $entity = (new DatabaseEntity())
            ->setHost($host)
            ->setUser($user)
            ->setPlainPassword($plainPassword)
            ->setPassword($password)
            ->setName($name);

        $this->validator->validate($entity, new DatabaseConstraint());
    }

    /**
     * @return iterable<string, array<null|string>>
     */
    public function provideEmptyValidCases(): iterable
    {
        yield 'empty_everything' => [null, null, null, null, null];
        yield 'empty_host' => [null, 'test', 'test', 'test', 'test'];
        yield 'empty_user' => ['test', null, 'test', 'test', 'test'];
        yield 'empty_password' => ['test', 'test', null, null, 'test'];
        yield 'empty_name' => ['test', 'test', 'test', 'test', null];
    }
}
