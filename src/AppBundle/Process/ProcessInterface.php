<?php

namespace AppBundle\Process;
use Symfony\Component\Process\Process;

/**
 * Interface ProcessInterface
 * @package AppBundle\Process
 */
interface ProcessInterface
{
    /**
     * Executes the process.
     */
    public function execute();

    /**
     * Configures the executor.
     *
     * @param array $data
     */
    public function configure(array $data = []);

    /**
     * Sets the process identifier
     *
     * @param string $name
     */
    public function setName($name);

    /**
     * Gets the process identifier
     *
     * @return string
     */
    public function getName();

    /**
     * Sets the process priority
     *
     * @param int $priority
     */
    public function setPriority($priority);

    /**
     * @return int The process priority.
     */
    public function getPriority();
}
