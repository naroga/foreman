<?php

namespace AppBundle\Process;

/**
 * Class DummyProcess
 * @package AppBundle\Process
 */
class DummyProcess implements ProcessInterface
{

    protected $priority = 3;

    /**
     * Executes the process.
     */
    public function execute()
    {
        sleep(rand(1, 3));
        //5% timeout chance.
        if (rand(1, 20) == 20) {
            sleep(rand(60));
        }
    }

    /**
     * Configures the executor.
     *
     * @param array $data
     */
    public function configure(array $data = [])
    {
        // TODO: Implement configure() method.
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return int The process priority.
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
