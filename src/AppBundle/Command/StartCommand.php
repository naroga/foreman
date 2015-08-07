<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Class StartCommand
 * @package AppBundle\Command
 */
class StartCommand extends ContainerAwareCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('foreman:processor:start')
            ->setDescription('Starts the Foreman Processor service.')
            ->addOption(
                'daemon',
                '-d',
                InputOption::VALUE_NONE,
                'Starts the service as a daemon service.'
            );
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('daemon')) {
            $phpPath = (new PhpExecutableFinder())->find();
            $process = new Process(
                $phpPath . ' app/console foreman:processor:start' . (($input->getOption('verbose') ? ' -v' : ''))
            );
            $process->setTimeout(0);
            $process->start();
        } else {
            $this->getContainer()->get('foreman.processor')->start($output);
        }
    }
}
