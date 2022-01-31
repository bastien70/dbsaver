<?php

declare(strict_types=1);

namespace App\Tests\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class SwitchStorageModeCommandTest extends KernelTestCase
{
    public function testSwitchToAws(): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:switch-storage-mode');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs([
            'AWS S3',
            'aws_s3 access id',
            'aws_s3 access secret',
            'aws_s3 bucket name',
            'aws_s3 region',
        ]);

        $commandTester->execute(['command' => $command->getName()]);
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Successfully switched to AWS S3 storage!', $output);
    }

    public function testSwitchToLocal(): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:switch-storage-mode');
        $commandTester = new CommandTester($command);
        $commandTester->setInputs([
            'Locally',
        ]);

        $commandTester->execute(['command' => $command->getName()]);
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('Successfully switched to Locally storage!', $output);
    }
}
