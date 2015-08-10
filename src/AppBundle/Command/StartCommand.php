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
    protected function execute(InputInterface $input, OutputInterface $output, $retried = false)
    {
        if ($this->getContainer()->get('foreman.accessor')->ping()) {
            $output->writeln('<error>The Foreman Processor server is already running.');
            return;
        }

        if ($input->getOption('daemon')) {
            $phpPath = (new PhpExecutableFinder())->find();
            $process = new Process(
                $phpPath . ' app/console foreman:processor:start' . (($input->getOption('verbose') ? ' -v' : ''))
            );
            $process->setTimeout(0);

            $output->writeln('<info>Starting daemon server...');

            $process->start();
            $accessor = $this->getContainer()->get('foreman.accessor');
            $start = time();

            $interval = $this->getContainer()->getParameter('foreman.processor')['interval'];

            //When we try to ping the server the first time, it almost always fails.
            //The server isn't started yet by the time we get here, so we wait.
            //Timeout = 3 seconds. Should be enough for all servers.
            while (!$accessor->ping() && (time() - $start < 3)) {
                sleep($interval);
            }

            if ($accessor->ping()) {
                $status = $accessor->status();
                $output->writeln(
                    '<info>The server was started successfully with PID ' . $status['pid'] . '</info>'
                );
            } else {
                $output->writeln('<error>The server could not be started at this moment.</error>');
                $output->writeln(
                    'Please check if the server port (' .
                    $this->getContainer()->getParameter('foreman.processor.port')
                    . ') is available.'
                );
                $output->writeln(
                    'If you have closed the server recently, the connection may not have released. Try again ' .
                    'in a few seconds.'
                );
            }

        } else {
            $output->writeln('<info>Starting the Foreman Processor...</info>');
            $this->getContainer()->get('foreman.processor')->start($output, $input->getOption('verbose'));
        }
    }
}
