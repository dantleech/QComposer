<?php

namespace DTL\QComposer\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;
use DTL\QComposer\QComposer;

class InstallCommand extends Command
{
    public function configure()
    {
        $this->setName('install');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $file = 'composer.json';
        if (!file_exists($file)) {
            throw new \Exception('No composer.json file in current directory');
        }

        $depConfigJson = file_get_contents($file);

        $composer = new QComposer($depConfigJson);
        $composer->setOutput($output);
        $composer->install();
    }
}
