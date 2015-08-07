<?php

namespace AppBundle\Processor;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Socket\Server;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class Processor
 * @package AppBundle\Processor
 */
class Processor
{

    /** @var int */
    protected $interval;

    /** @var int */
    protected $port = 3440;

    /** @var LoopInterface */
    protected $loop;

    /**
     * Class constructor
     *
     * @param array $processorConfiguration Configuration array, parsed from app/config/services.yml
     */
    public function __construct(array $processorConfiguration)
    {
        $this->interval = $processorConfiguration['interval'];
        $this->port = $processorConfiguration['port'];
    }

    /**
     * Starts the processor server.
     *
     * @param OutputInterface $output
     * @throws \React\Socket\ConnectionException
     */
    public function start(OutputInterface &$output)
    {
        $this->loop = Factory::create();
        $socket = new Server($this->loop);

        $http = new \React\Http\Server($socket);
        $http->on('request', [$this, 'manageRequest']);

        $socket->listen($this->port);

        $this->loop->addPeriodicTimer($this->interval, [$this, 'manageQueue']);
        $this->loop->run();
    }

    /**
     * Manages a request, made to the Http server.
     *
     * @param $request
     * @param $response
     */
    public function manageRequest($request, $response)
    {
        //Stub
    }

    /**
     * Manages the queue. Dispatches processes, kills timed out processes, etc.
     */
    public function manageQueue()
    {
        //Stub
    }
}
