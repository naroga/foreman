<?php

namespace AppBundle\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class StubCommand
 * @package AppBundle\Command
 */
class StubCommand extends ContainerAwareCommand
{
    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this
            ->setName('foreman:stub')
            ->setDescription('Fakes <number> processes. They will only sleep. 4% chance of timeout.')
            ->addArgument(
                'number',
                InputArgument::REQUIRED,
                'Number of processes to stub'
            );
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $client = $this->getContainer()->get('foreman.accessor')->getClient();

        for ($i = 0; $i < $input->getArgument('number'); $i++) {
            $output->writeln(
                'Stubbing <info>' . ($i + 1) . '</info> of <info>' . $input->getArgument('number') . '</info>'
            );
            $client->post('/add-process', [
                'form_params' => [
                    'type' => 'dummy'
                ]
            ]);
        }
    }
}
