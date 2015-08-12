<?php

namespace AppBundle\Processor;

use AppBundle\Event\ProcessAddedEvent;
use AppBundle\Event\ProcessFailedEvent;
use AppBundle\Event\ProcessFinishedEvent;
use AppBundle\Event\ProcessStartedEvent;
use AppBundle\Process\ProcessInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Socket\Server;
use React\Stream\BufferedSink;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Debug\TraceableEventDispatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use Symfony\Component\PropertyAccess\PropertyAccess;

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

    /** @var Server */
    protected $socket;

    /** @var \SplPriorityQueue */
    protected $queue;

    /** @var array */
    protected $processList = [];

    /** @var \SplFixedArray */
    protected $workers;

    /** @var string */
    protected $phpPath = 'php';

    /** @var int */
    protected $timeout = 30;

    /** @var TraceableEventDispatcher */
    protected $eventDispatcher;

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
        $this->timeout = $processorConfiguration['timeout'];

        $this->queue = new \SplPriorityQueue();
        $this->queue->setExtractFlags(\SplPriorityQueue::EXTR_DATA);
        $this->workers = new \SplFixedArray($processorConfiguration['workers']);
        $this->phpPath = (new PhpExecutableFinder())->find();

        $this->eventDispatcher = $kernel->getContainer()->get('event_dispatcher');
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
        $this->socket = new Server($this->loop);

        $http = new \React\Http\Server($this->socket);
        $http->on('request', [$this, 'manageRequest']);

        $this->socket->listen($this->port, $this->host);

        $this->registerListeners();

        $this->loop->addPeriodicTimer($this->interval, [$this, 'manageQueue']);
        $this->loop->run();
    }

    /**
     * Manages a request, made to the Http server.
     *
     * @param \React\Http\Request $request
     * @param \React\Http\Response $response
     */
    public function manageRequest($request, $response)
    {

        $requestData = [
            $request->getHeaders()['Host'] . $request->getPath(),
            $request->getMethod(),
            array_merge($request->getQuery(), $request->getPost()),
            [],
            $request->getFiles(),
            []
        ];

        $contentType = isset($request->getHeaders()['Content-Type']) ?
            $request->getHeaders()['Content-Type'] :
            'application/x-www-form-urlencoded';

        if (strtolower($contentType) == 'application/x-www-form-urlencoded') {
            $requestData[] = http_build_query($request->getPost());
        } else {
            $requestData[] = $request->getBody();
        }

        echo $contentType;

        //Creates the Symfony Request from the React Request.
        $sRequest = Request::create(...$requestData);

        /** @var Response $sResponse */
        $sResponse = $this->kernel->handle($sRequest);
        $response->writeHead($sResponse->getStatusCode());
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

        $this->clearIdleWorkers();

        foreach ($this->workers as $index => $worker) {
            if (!$worker && count($this->queue) > 0) {
                $processName = $this->queue->extract();
                $this->workers[$index] = new Process(
                    $this->phpPath . ' app/console foreman:dispatch ' . $processName .
                    ' --env=' . $this->kernel->getEnvironment()
                );
                $this->workers[$index]->setTimeout($this->timeout);
                $this->workers[$index]->start();
                $this->eventDispatcher->dispatch(
                    'foreman.process.started',
                    new ProcessStartedEvent($processName)
                );
            }
        }
    }

    /**
     * Checks if the SIGTERM is presente in the server.
     */
    protected function checkSigterm()
    {
        if ($this->sigterm) {
            $this->loop->stop();
            $this->socket->shutdown();
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

    /**
     * Adds a new process to the queue.
     *
     * @param ProcessInterface $process The process to be added to the queue.
     * @return bool Success
     */
    public function addProcess(ProcessInterface $process)
    {
        $processName = substr(str_shuffle(md5(microtime())), 0, 10);

        //Avoid process name collision.
        while (array_key_exists($processName, $this->processList)) {
            $processName = substr(str_shuffle(md5(microtime())), 0, 10);
        }

        $process->setName($processName);
        $this->processList[$processName] = $process;
        $this->queue->insert($processName, $process->getPriority());

        $this->eventDispatcher->dispatch(
            'foreman.process.added',
            new ProcessAddedEvent($processName)
        );

        return $processName;
    }

    /**
     * Clears all idle workers.
     */
    protected function clearIdleWorkers()
    {
        /**
         * @var int $index
         * @var Process $worker
         */
        for ($i = 0; $i < count($this->workers); $i++) {
            $worker = $this->workers[$i];

            if (!$worker) {
                continue;
            }

            $processName = array_keys(array_filter($this->processList, function ($item) use ($worker) {
                $exploded = (explode(" ", $worker->getCommandLine()));
                $name = $exploded[count($exploded) - 2];
                return $item->getName() == $name;
            }))[0];

            if (!$worker->isRunning() && $worker->isSuccessful()) {
                $this->eventDispatcher->dispatch(
                    'foreman.process.finished',
                    new ProcessFinishedEvent($processName)
                );
                unset($worker);
                $this->workers[$i] = null;
                unset($this->processList[$processName]);
            } elseif (!$worker->isRunning() && !$worker->isSuccessful()) {
                $this->eventDispatcher->dispatch(
                    'foreman.process.failed',
                    new ProcessFailedEvent($processName, 'FAILED')
                );
                if (!empty($worker->getOutput())) {
                    $this->output->writeln('<error>Output: ' . $worker->getOutput() . '</error>');
                }
                unset($worker);
                $this->workers[$i] = null;
                unset($this->processList[$processName]);
            } elseif ($worker->isRunning()) {
                try {
                    $worker->checkTimeout();
                } catch (ProcessTimedOutException $e) {
                    $this->eventDispatcher->dispatch(
                        'foreman.process.failed',
                        new ProcessFailedEvent($processName, 'TIMEOUT')
                    );
                    unset($worker);
                    $this->workers[$i] = null;
                    unset($this->processList[$processName]);
                }
            }
        }
    }

    /**
     * Gets a process from the list, by name
     *
     * @param string $name
     * @return ProcessInterface
     */
    public function getProcess($name)
    {
        return $this->processList[$name];
    }

    /**
     * Registers all listeners.
     */
    protected function registerListeners()
    {
        $printEvents = [
            'foreman.process.added' => [
                'message' => '<info>Process %s was added to the queue.</info>',
                'properties' => ['name']
            ],
            'foreman.process.finished' => [
                'message' => '<info>Process %s has ended successfully.</info>',
                'properties' => ['name']
            ],
            'foreman.process.failed' => [
                'message' => '<error>Process %s failed. Reason: %s.</error>',
                'properties' => ['name', 'reason']
            ],
            'foreman.process.started' => [
                'message' => '<info>Process %s has started.</info>',
                'properties' => ['name']
            ]
        ];

        $output = $this->output;

        foreach ($printEvents as $index => $messageData) {
            $this->eventDispatcher->addListener($index, function ($event) use (&$output, $messageData) {
                $properties = $messageData['properties'];
                $values = [];
                $accessor = PropertyAccess::createPropertyAccessor();
                foreach ($properties as $property) {
                    $values[] = $accessor->getValue($event, $property);
                }
                $output->writeln(sprintf($messageData['message'], ...$values));
            });
        }
    }
}
