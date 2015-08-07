<?php

namespace AppBundle\Processor;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Processor
 * @package AppBundle\Processor
 */
class Processor
{
    public function start(OutputInterface &$output)
    {
        $output->writeln('<info>Starting server...</info>');
        while (true) {
            sleep(1);
        }
    }
}
