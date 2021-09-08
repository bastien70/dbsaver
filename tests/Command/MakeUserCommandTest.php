<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Exception\MissingInputException;
use Symfony\Component\Console\Tester\CommandTester;

final class MakeUserCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:make-user');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs(['contact@test.com', 'test', 'en', true, 'ROLE_ADMIN']);

        $commandTester->execute(['command' => $command->getName()]);
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('User contact@test.com has been successfully created! You can now log in.', $output);

        $commandTester->execute(['command' => $command->getName()]);
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Email address contact@test.com is already used.', $output);
    }

    /**
     * @dataProvider provideInvalidCases
     */
    public function testExecuteWithInvalidInput(string $email, string $password, string $role): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:make-user');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs([$email, $password, $role]);

        $this->expectException(MissingInputException::class);
        $commandTester->execute(['command' => $command->getName()]);
    }

    public function provideInvalidCases(): iterable
    {
        yield 'no_email' => ['', '', '', ''];
        yield 'invalid_email' => ['contact', '', '', ''];
        yield 'no_password' => ['contact@test.com', '', '', ''];
        yield 'no_locale' => ['contact@test.com', 'test', '', ''];
    }
}
