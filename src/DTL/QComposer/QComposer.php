<?php

namespace DTL\QComposer;

use Symfony\Component\Console\Output\OutputInterface;

class QComposer
{
    protected $depConfig;
    protected $output;

    public function __construct($depConfig)
    {
        $depConfig = json_decode($depConfig, true);
        $this->depConfig = $depConfig;
    }

    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function install()
    {
        foreach ($this->depConfig['require'] as $require => $version) {
            $parts = explode('/', $require);
            $package = sprintf(
                'https://packagist.org/packages/%s/%s.json',
                $parts[0], $parts[1]
            );

            $this->output->writeln('Fetching dep info: '.$package);
            $json = file_get_contents($package);
            $package = json_decode($json, true);
            $package = $package['package'];

            if (!isset($package['versions'][$version])) {
                $this->output->writeln(sprintf(
                    '<error>Package "%s" does not exist in version "%s", has versions: %s</error>',
                    $package['name'],
                    $version,
                    implode(',', array_keys($package['versions']))
                ));
            }

            $version = $package['versions'][$version];
            $source = $version['source'];

            $this->exec(sprintf(
                'git clone %s %s',
                $source['url'],
                $package['name']
            ));
            $this->exec(sprintf(
                'cd %s && git checkout %s',
                $package['name'],
                $source['reference']
            ));
            $this->exec(
                'cd ..'
            );
        }
    }

    public function exec($command)
    {
        $this->output->writeln('<comment>'.$command.'</comment>');
        exec($command);
    }
}
