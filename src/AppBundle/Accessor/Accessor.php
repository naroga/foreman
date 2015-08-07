<?php

namespace AppBundle\Accessor;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use JMS\Serializer\Serializer;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Accessor
 * @package AppBundle\Accessor
 */
class Accessor
{

    /** @var Client */
    protected $client;

    /** @var Serializer */
    protected $serializer;

    /**
     * Class constructor
     *
     * @param string $host The host, with the port (http://hostname:port)
     */
    public function __construct($host, Serializer $serializer)
    {
        $this->client = new Client(['base_uri' => $host]);
        $this->serializer = $serializer;
    }

    /**
     * Sends the SIGTERM to the current Foreman Processor.
     *
     * @return bool
     */
    public function stop()
    {
        /** @var ResponseInterface $response */
        $response = $this->client->get('/stop');
        return $this->serializer->deserialize(
            $response->getBody(),
            'array',
            'json'
        );
    }

    /**
     * Pings the server.
     * Basically serves as a 'is the server up?' test.
     *
     * @return bool
     */
    public function ping()
    {
        try {
            $response = $this->client->get('/ping');
        } catch (GuzzleException $e) {
            return false;
        }
        return true;
    }
}
