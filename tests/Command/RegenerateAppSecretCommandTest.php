<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class RegenerateAppSecretCommandTest extends KernelTestCase
{
    public function testExecute(): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:regenerate-app-secret');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['command' => $command->getName()]);
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('The new APP_SECRET env var has been regenerated!', $output);
    }
}
