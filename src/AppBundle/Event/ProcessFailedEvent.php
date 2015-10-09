<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ProcessFailedEvent
 * @package AppBundle\Event
 */
class ProcessFailedEvent extends Event
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $reason;

    /**
     * @var string
     */
    protected $output;

    /**
     * Class constructor
     *
     * @param string $name
     * @param string $reason
     * @param string $output
     */
    public function __construct($name, $reason, $output = '')
    {
        $this->name = $name;
        $this->reason = $reason;
        $this->output = $output;
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
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * @return string
     */
    public function getOutput()
    {
        return $this->output;
    }
}