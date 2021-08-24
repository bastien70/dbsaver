<?php

declare(strict_types=1);

namespace App\Command;

use sixlive\DotenvEditor\DotenvEditor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

abstract class AbstractDotEnvCommand extends Command
{
    protected function getDotenvEditor(InputInterface $input): DotenvEditor
    {
        $fileName = 'test' === $input->getOption('env') ? '.env.test.local' : '.env.local';
        $filePath = __DIR__ . '/../../' . $fileName;
        if (!file_exists($filePath)) {
            touch($filePath);
        }

        $editor = new DotenvEditor();
        $editor->load($filePath);

        return $editor;
    }
}
