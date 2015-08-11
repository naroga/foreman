<?php

namespace AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ProcessStartedEvent
 * @package AppBundle\Event
 */
class ProcessStartedEvent extends Event
{
    /** @var string */
    protected $name;

    /**
     * Class constructor
     *
     * @param string $name
     */
    public function __construct($name)
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
}
