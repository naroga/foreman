<?php

namespace AppBundle\Processor;

use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Socket\Server;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class Processor
 * @package AppBundle\Processor
 */
class Processor
{

    /** @var int */
    protected $interval;

    /** @var string */
    protected $host = '127.0.0.1';

    /** @var int */
    protected $port = 3440;

    /** @var LoopInterface */
    protected $loop;

    /** @var KernelInterface */
    protected $kernel;

    /** @var OutputInterface */
    protected $output;

    /** @var bool */
    protected $verbose = false;

    /** @var bool */
    protected $sigterm = false;

    /**
     * Class constructor
     *
     * @param array $processorConfiguration Configuration array, parsed from app/config/services.yml
     * @param KernelInterface $kernel The Symfony HttpKernel.
     */
    public function __construct(array $processorConfiguration, KernelInterface $kernel)
    {
        $this->kernel = $kernel;

        $this->interval = $processorConfiguration['interval'];
        $this->port = $processorConfiguration['port'];
        $this->host = $processorConfiguration['host'];
    }

    /**
     * Starts the processor server.
     *
     * @param OutputInterface $output
     * @param bool $verbose
     * @throws \React\Socket\ConnectionException
     */
    public function start(OutputInterface &$output, $verbose = false)
    {
        $this->output = $output;
        $this->verbose = $verbose;

        $this->loop = Factory::create();
        $socket = new Server($this->loop);

        $http = new \React\Http\Server($socket);
        $http->on('request', [$this, 'manageRequest']);

        $socket->listen($this->port, $this->host);

        $this->loop->addPeriodicTimer($this->interval, [$this, 'manageQueue']);
        $this->loop->run();
    }

    /**
     * Manages a request, made to the Http server.
     *
     * @param \React\Http\Request $request
     * @param $response
     */
    public function manageRequest($request, $response)
    {
        //Creates the Symfony Request from the React Request.
        $sRequest = Request::create(
            $request->getHeaders()['Host'] . $request->getPath(),
            $request->getMethod(),
            $request->getQuery()
        );

        /** @var Response $sResponse */
        $sResponse = $this->kernel->handle($sRequest);
        $response->writeHead($sResponse->getStatusCode(), ['Content-type' => 'application/json']);
        $response->end($sResponse->getContent());
    }

    /**
     * Manages the queue. Dispatches processes, kills timed out processes, etc.
     */
    public function manageQueue()
    {
        //Checks if there is a SIGTERM present.
        if ($this->checkSigterm()) {
            return true;
        };
    }

    /**
     * Checks if the SIGTERM is presente in the server.
     */
    protected function checkSigterm()
    {
        if ($this->sigterm) {
            $this->loop->stop();
            $this->output->writeln('The Foreman Processor has been stopped.');
        }

    }

    /**
     * Sends the SIGTERM to the Foreman Processor.
     */
    public function stop()
    {
        $this->output->writeln('Stopping the Foreman Processor.');
        $this->sigterm = true;
    }
}
