<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
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
}
