<?php

namespace pxgamer\AudioSort;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SortCommand extends Command
{
    protected $allowedFilename = '/(([A-Z]{3}_(?:\d+){2}_(?:\d+){3}_(?:\d+){3})_a(?:\d+?))\.(?:\w){3}/i';

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('sort')
            ->setDescription('Add a new deploy key.')
            ->addArgument('directory', InputArgument::OPTIONAL, getcwd());
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
        $directory = $input->getArgument('directory');

        if (!$directory) {
            throw new \ErrorException('No directory specified.');
        }

        $output->writeln([
            '<comment>Sorting audio content:</comment>',
            '<comment>-------------------------------------------------------------</comment>',
            ''
        ]);

        $directoryIterator = new \DirectoryIterator($directory);

        foreach ($directoryIterator as $item) {
            if ($item->isFile()) {
                if (preg_match($this->allowedFilename, $item->getBasename(), $sections)) {
                    $audioDirectory = $item->getPath() . DIRECTORY_SEPARATOR . $sections[2];
                    if (!is_dir($audioDirectory)) {
                        mkdir($audioDirectory, null, true);
                    }
                    switch ($item->getExtension()) {
                        case 'wav':
                            $srcDirectory = $audioDirectory . DIRECTORY_SEPARATOR . 'src';
                            if (!is_dir($srcDirectory)) {
                                mkdir($srcDirectory, null, true);
                            }
                            rename($item->getRealPath(), $srcDirectory . DIRECTORY_SEPARATOR . $item->getBasename());
                            break;
                        case 'mp3':
                            rename($item->getRealPath(), $audioDirectory . DIRECTORY_SEPARATOR . $item->getBasename());
                            break;
                        default:
                            break;
                    }
                    $output->writeln('Moved ' . $item->getBasename());
                }
            }
        }
    }
}