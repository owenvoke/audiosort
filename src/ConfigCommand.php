<?php

namespace pxgamer\AudioSort;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ConfigCommand
 * @package pxgamer\AudioSort
 */
class ConfigCommand extends Command
{
    const PATH = __DIR__ . '/../config.json';
    /**
     * @var SymfonyStyle
     */
    public $oOutput;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('config')
            ->setDescription('Add a config parameter to the config.json')
            ->addArgument('item', InputArgument::REQUIRED)
            ->addArgument('value', InputArgument::REQUIRED);
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     * @throws \ErrorException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $item = $input->getArgument('item');
        $value = $input->getArgument('value');

        $this->oOutput = new SymfonyStyle($input, $output);

        if (!file_exists(self::PATH)) {
            file_put_contents(self::PATH, '{}');
        }

        $currentConfig = json_decode(file_get_contents(__DIR__ . '/../config.json'), true);
        $currentConfig[$item] = $value;
        file_put_contents(__DIR__ . '/../config.json', json_encode($currentConfig));
    }
}