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
     * Class constructor
     *
     * @param string $name
     * @param string $reason
     */
    public function __construct($name, $reason)
    {
        $this->name = $name;
        $this->reason = $reason;
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
}