<?php

namespace AppBundle\Process;
use AppBundle\Exception\InvalidRequestException;
use Symfony\Component\HttpFoundation\Request;
use GuzzleHttp\Client;

/**
 * Class RequestProcess
 * @package AppBundle\Process
 */
class RequestProcess implements ProcessInterface
{
    /** @var string */
    protected $name;

    /** @var int */
    protected $priority = 3;

    /** @var string */
    protected $url;

    /** @var string */
    protected $method;

    /** @var string */
    protected $payload;

    /** @var array */
    protected $headers = [];

    /** @var array */
    protected $query = [];


    /**
     * @inheritDoc
     */
    public function execute()
    {
        $client = new Client();

        $post = [];
        parse_str($this->query, $post);

        $client->request(
            $this->method,
            $this->url,
            [
                'headers' => $this->headers,
                'body' => $this->payload,
                'form_params' => $post
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function configure(Request $data = null)
    {

        if (!($data->get('url')) || !($data->get('method'))) {
            throw new InvalidRequestException(
                'A request for this type of process requires both a \'url\' and a \'method\' variables in the POST ' .
                'request.'
            );
        }

        $this->url = $data->get('url');
        $this->method = $data->get('method');
        $this->payload = $data->get('payload');
        $this->query = $data->get('query');
        $this->headers = $data->get('headers') ? unserialize($data->get('headers')) : [];
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