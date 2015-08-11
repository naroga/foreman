<?php

namespace AppBundle\Controller;

use AppBundle\Exception\InvalidRequestException;
use AppBundle\Process\ProcessFactory;
use AppBundle\Process\ProcessInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ProcessorController
 * @package AppBundle\Controller
 */
class ProcessorController extends Controller
{
    /**
     * Stops the Processor.
     *
     * @return JsonResponse
     */
    public function stopAction()
    {
        $this->get('foreman.processor')->stop();
        return new JsonResponse(['success' => true, 'message' => 'SIGTERM Received']);
    }

    /**
     * This action just needs to be reachable for ping tests.
     *
     * @return JsonResponse
     */
    public function pingAction()
    {
        return new JsonResponse(['success' => true, 'message' => 'pong']);
    }

    /**
     * Gets the server status.
     *
     * @return JsonResponse
     */
    public function statusAction()
    {
        return new JsonResponse([
            'success' => true,
            'pid' => getmypid()
        ]);
    }

    /**
     * Adds a new Process to the queue.
     *
     * @param Request $request
     * @return JsonResponse The Response
     * @throws InvalidRequestException
     */
    public function addProcessAction(Request $request)
    {

        $priority = $request->get('priority') | 3; //3 is the default priority.
        $processType = $request->get('type');

        if (!$processType) {
            return new JsonResponse(
                [
                    'success' => false,
                    'message' => "'addProcessAction' requires a 'type' variable from the POST Request. "
                ],
                500
            );
        }

        /** @var ProcessInterface $process */
        $process = ProcessFactory::create($processType, $request);
        $process->setPriority($priority);
        $process->configure($request);

        $name = $this->get('foreman.processor')->addProcess($process);

        return new JsonResponse(
            ['success' => true, 'message' => 'Process added to the queue.', "name" => $name]
        );
    }

    /**
     * Gets the process by name
     *
     * @param string $name
     * @return JsonResponse
     */
    public function getProcessAction($name)
    {
        $process = $this->get('foreman.processor')->getProcess($name);

        $serializer = $this->get('jms_serializer');

        return new JsonResponse(
            [
                'success' => true,
                'process' => serialize($process)
            ]
        );
    }
}
