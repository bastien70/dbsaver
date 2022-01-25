<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class YamlService
{
    public const CONFIG_INLINE_LEVEL = 5;
    private ?string $absoluteFilePath;

    private Finder $finder;

    public function __construct(string $fileDir, string $fileName)
    {
        $this->finder = new Finder();
        $this->setAbsoluteFilePath($this->buildFilePath($fileDir, $fileName));
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

    private function buildFilePath(string $fileDir, string $fileName): null|false|string
    {
        $filePath = null;
        $this->finder->files()->name(sprintf('%s.yaml', $fileName))->in(sprintf('%s/../../%s', __DIR__, $fileDir));

        if ($this->finder->hasResults()) {
            foreach ($this->finder as $file) {
                $filePath = $file->getRealPath();
            }
        }

        return $filePath;
    }
}
