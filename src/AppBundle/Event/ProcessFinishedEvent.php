<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ProcessFinishedEvent
 * @package AppBundle\Event
 */
class ProcessFinishedEvent extends Event
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $output;

    /**
     * Class constructor
     *
     * @param string $name
     * @param string $output
     */
    public function __construct($name, $output)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }
}
