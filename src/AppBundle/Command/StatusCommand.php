<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StatusCommand
 * @package AppBundle\Command
 */
class StatusCommand extends ContainerAwareCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('foreman:processor:status')
            ->setDescription('Checks the server status.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $status = $this->getContainer()->get('foreman.accessor')->status();

        if ($status === false) {
            $output->writeln('Status: <error>NOT RUNNING</error>');
            return;
        }

        $output->writeln('Status: <info>RUNNING</info>');
        $output->writeln('PID: <info>' . $status['pid'] . '</info>');
    }
}
