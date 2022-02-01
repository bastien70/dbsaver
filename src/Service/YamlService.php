<?php

declare(strict_types=1);

namespace App\Service;

use function sprintf;
use Symfony\Component\Yaml\Yaml;

class YamlService
{
    public const CONFIG_INLINE_LEVEL = 5;

    private ?string $absoluteFilePath;

    public function __construct(string $fileDir, string $fileName, string $projectDir)
    {
        $this->setAbsoluteFilePath($this->buildFilePath($fileDir, $fileName, $projectDir));
    }

    public function getFileContent(): mixed
    {
        return Yaml::parseFile($this->getAbsoluteFilePath());
    }

    public function writeFileContent(mixed $newContent): void
    {
        $yaml = Yaml::dump($newContent, self::CONFIG_INLINE_LEVEL);

        file_put_contents($this->getAbsoluteFilePath(), $yaml);
    }

    public function getAbsoluteFilePath(): ?string
    {
        return $this->absoluteFilePath;
    }

    public function setAbsoluteFilePath(?string $absoluteFilePath): void
    {
        $this->absoluteFilePath = $absoluteFilePath;
    }

    private function buildFilePath(string $fileDir, string $fileName, string $projectDir): string
    {
        return sprintf('%s/%s/%s.yaml', $projectDir, $fileDir, $fileName);
    }
}
