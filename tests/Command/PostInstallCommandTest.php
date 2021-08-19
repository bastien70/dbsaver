<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Exception\MissingInputException;
use Symfony\Component\Console\Tester\CommandTester;

final class PostInstallCommandTest extends KernelTestCase
{
    /**
     * @dataProvider provideInvalidCases
     */
    public function testExecuteWithInvalidInput(string $dsn): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:post-install');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs([$dsn]);

        $this->expectException(MissingInputException::class);
        $commandTester->execute(['command' => $command->getName()]);
    }

    public function provideInvalidCases(): iterable
    {
        yield 'no_dsn' => [''];
    }
}
