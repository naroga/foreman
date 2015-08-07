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

            if ($this->getContainer()->get('foreman.accessor')->ping()) {
                //WTF, I don't know why but subtracting 2 is needed to display the correct result.
                $output->writeln(
                    '<info>The server has been started successfully with PID ' . ($process->getPid() - 2) . '</info>'
                );
            } else {
                $output->writeln('<error>The server could not be started at this moment.</error>');
                $output->writeln(
                    'Please check if the server port (' . $this->getContainer()->getParameter('foreman.processor.port')
                    . ') is available.'
                );
                $output->writeln(
                    'If you have closed the server recently, the connection may not have released. Try again ' .
                    'in a few seconds.'
                );
            }

        } else {
            $this->getContainer()->get('foreman.processor')->start($output, $input->getOption('verbose'));
        }
    }
}
