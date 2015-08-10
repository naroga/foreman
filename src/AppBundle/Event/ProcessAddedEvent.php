<?php

namespace AppBundle\Event;
use Symfony\Component\EventDispatcher\Event;

/**
 * Class ProcessAddedEvent
 * @package AppBundle\Event
 */
class ProcessAddedEvent extends Event
{
    /**
     * @var string
     */
    protected $name;

    /**
     * Class constructor
     *
     * @param $name
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
