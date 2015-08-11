<?php

namespace AppBundle\Process;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class DummyProcess
 * @package AppBundle\Process
 */
class DummyProcess implements ProcessInterface
{

    /** @var int */
    protected $priority = 3;

    /** @var string */
    protected $name = null;

    /**
     * @inheritDoc
     */
    public function execute()
    {
        sleep(rand(1, 3));
        //4% timeout chance.
        if (rand(1, 25) == 25) {
            sleep(60);
        }
    }

    /**
     * @inheritDoc
     */
    public function configure(Request $data = null)
    {

    }

    /**
     * @inheritDoc
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @inheritDoc
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @inheritDoc
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return $this->name;
    }
}
