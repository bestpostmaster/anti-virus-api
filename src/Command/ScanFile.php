<?php

declare(strict_types=1);

namespace App\Command;

use App\Repository\HostedFileRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ScanFile extends Command
{
    protected static $defaultName = 'app:scan-file';

    private HostedFileRepository $hostedFileRepository;
    private string $hostingDirectory;
    private string $projectDirectory;

    public function __construct(HostedFileRepository $hostedFileRepository, string $hostingDirectory, string $projectDirectory)
    {
        $this->hostedFileRepository = $hostedFileRepository;
        $this->hostingDirectory = $hostingDirectory;
        $this->projectDirectory = $projectDirectory;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp("This command launches an antivirus scan")
        ;

        $this
            ->addArgument('f', InputArgument::REQUIRED, 'The file name')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Antivirus scan',
            '==============',
            '',
        ]);

        $output->writeln('File: '.$input->getArgument('f'));
        $fullPath = $this->projectDirectory.$this->hostingDirectory.$input->getArgument('f');

        if (!file_exists($fullPath)) {

            $output->writeln([
                '<!!!> This file does not exist : '.$fullPath,
                '==========================================================================',
                '',
            ]);

            return 1;
        }

        return 0;
    }
}