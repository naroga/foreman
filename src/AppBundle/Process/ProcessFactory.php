<?php

namespace AppBundle\Process;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ProcessFactory
 * @package AppBundle\Process
 */
class ProcessFactory
{
    /**
     * Spawns a process container, for a given type.
     *
     * @param string $type The process type (PHP script, shell script, curl, etc)
     * @param Request $data Additional data.
     * @return DummyProcess
     */
    public static function create($type, Request $data = null)
    {
        switch ($type) {
            case 'dummy':
                return new DummyProcess();
            case 'request':
                return new RequestProcess();
        }
    }
}
