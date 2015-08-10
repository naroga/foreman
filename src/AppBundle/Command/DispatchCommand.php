<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class DispatchCommand
 * @package AppBundle\Command
 */
class DispatchCommand extends ContainerAwareCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('foreman:dispatch')
            ->setDescription('Dispatches a process by name.')
            ->addArgument(
                'process-name',
                InputArgument::REQUIRED,
                'The process name.'
            );
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $process =
            $this->getContainer()->get('foreman.accessor')->getProcess($input->getArgument('process-name'));

        $process->execute();
    }
}
