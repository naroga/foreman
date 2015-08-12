<?php

namespace AppBundle\Process;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

/**
 * Class PhpProcess
 * @package AppBundle\Process
 */
class PhpProcess implements ProcessInterface
{
    /** @var int */
    protected $priority = 3;

    /** @var string */
    protected $name;

    /** @var string */
    protected $script;

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $filesystem = new Filesystem();
        if (!$filesystem->exists('app/cache/tmp')) {
            $filesystem->mkdir('app/cache/tmp');
        }
        $filename = md5($this->script) . '.php';
        $filesystem->dumpFile('app/cache/tmp/' . md5($this->script) . '.php', $this->script);

        $phpPath = (new PhpExecutableFinder())->find();
        $process = new Process($phpPath . ' app/cache/tmp/' . $filename);
        $process->setTimeout(0);
        $process->run();
        echo $process->getOutput();
    }

    /**
     * @inheritDoc
     */
    public function configure(Request $data = null)
    {
        $this->script = urldecode($data->getContent());
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
}
