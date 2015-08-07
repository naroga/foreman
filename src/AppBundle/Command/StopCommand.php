<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StopCommand
 * @package AppBundle\Command
 */
class StopCommand extends ContainerAwareCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('foreman:processor:stop')
            ->setDescription('Stops the foreman processor.');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $accessor = $this->getContainer()->get('foreman.accessor');

        if (!$accessor->ping()) {
            $output->writeln('<error>The server is not reachable.</error>');
            return;
        }

        $response = $accessor->stop();
        if ($response['success']) {
            $output->writeln('A <error>SIGTERM</error> has been sent to the Foreman Processor.');
        }
    }
}
