<?php

namespace pxgamer\AudioSort;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class SortCommand
 * @package pxgamer\AudioSort
 */
class SortCommand extends Command
{
    /**
     * @var array
     */
    public $config = [];
    /**
     * @var nulL|SymfonyStyle
     */
    public $oOutput;
    /**
     * @var null|string
     */
    public $providedPattern;
    /**
     * @var null|string
     */
    protected $allowedFilename = null;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('sort')
            ->setDescription('Sort the specified directory.')
            ->addArgument('directory', InputArgument::OPTIONAL, getcwd())
            ->addOption('pattern', 'p', InputOption::VALUE_OPTIONAL);
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
        $this->providedPattern = $input->getOption('pattern');

        $this->oOutput = new SymfonyStyle($input, $output);

        if (file_exists(__DIR__ . '/../config.json')) {
            $this->config = json_decode(file_get_contents(__DIR__ . '/../config.json'), true);
        }

        $this->allowedFilename = $this->providedPattern ?? $this->config['pattern'] ?? null;

        if (!$this->allowedFilename) {
            throw new \ErrorException('No pattern specified.');
        }

        if (!$directory) {
            throw new \ErrorException('No directory specified.');
        }

        $this->oOutput->block([
            'Sorting audio content:',
            '-------------------------------------------------------------'
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
                    $this->oOutput->text('Moved ' . $item->getBasename());
                }
            }
        }
    }
}
