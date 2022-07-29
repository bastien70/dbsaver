<?php

declare(strict_types=1);

namespace App\Tests\Command;

use function sprintf;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class BackupCommandTest extends KernelTestCase
{
    /**
     * @dataProvider provideParams
     */
    public function testConnections(string $dbname, string $host, string $port): void
    {
        try {
            $connection = new \PDO($this->getDsn($host, $port, $dbname), 'root', 'root');
            $connection = null;

            dump(true);
        } catch (\Exception $e) {
            dump(false);
        }

        self::assertSame(1, 1);
    }

    public function testExecute(): void
    {
        $kernel = self::createKernel();
        $application = new Application($kernel);

        $command = $application->find('app:backup');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['command' => $command->getName()]);
        $output = $commandTester->getDisplay();
        self::assertStringContainsString('2 errors happened', $output);
    }

    private function getDsn(string $host, string $port, string $name): string
    {
        return sprintf(
            'mysql:host=%s:%s;dbname=%s',
            $host,
            $port,
            $name,
        );
    }

    private function provideParams(): iterable
    {
        yield [
            'dbname' => 'dbsaver',
            'host' => '127.0.0.1',
            'port' => '3306',
        ];

        yield [
            'dbname' => 'dbsaver_test',
            'host' => '127.0.0.1',
            'port' => '3306',
        ];

        yield [
            'dbname' => 'dbsaver',
            'host' => 'localhost',
            'port' => '3306',
        ];

        yield [
            'dbname' => 'dbsaver_test',
            'host' => 'localhost',
            'port' => '3306',
        ];

        yield [
            'dbname' => 'dbsaver',
            'host' => '127.0.0.1',
            'port' => '3307',
        ];

        yield [
            'dbname' => 'dbsaver_test',
            'host' => '127.0.0.1',
            'port' => '3307',
        ];

        yield [
            'dbname' => 'dbsaver',
            'host' => 'localhost',
            'port' => '3307',
        ];

        yield [
            'dbname' => 'dbsaver_test',
            'host' => '127.0.0.1',
            'port' => '3307',
        ];
    }
}
