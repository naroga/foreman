Processes
=========

In this section, you will learn how to add new processes to the Foreman Processor queue.
All processes must be added using the RESTful API, with a `POST` request to the `/add-process`
URI. We strongly advice you to use `guzzlehttp/guzzle` to spawn your requests. This is what
we are going to use for this example.

The URI above always requires at least two parameters: `type` and `priority`. 

    $client = new GuzzleHttp\Client;
    $client->post('http://127.0.0.1:3440/add-process', [
        'form_params' => [
            'type' => 'dummy',
            'priority' => 3
        ]
    ]);

Choosing your process type
--------------------------

You can see in the previous snippet, we passed `dummy` as the process `type`. The `dummy` type
is an internal type that does nothing. It spawns a process that idles for a random amount of time between
1 and 3 seconds before exiting successfully. It also has a 4% chance (random) to timeout. So, if your
goal is to only test the queue, you can spawn one (or many) dummy processes with the script above.

Now, if you actually plan on doing something, you should choose from one of the types below:

* [Request](process-request.md)
* [PHP](process-php.md)

Each kind of request may have a different set of required variables, so you will have to proceed to
their specific reference pages to see how to actually spawn them.

The Server response is a JSON string, in the following format:

    {"success": true, "message": "Process added to the queue.", "name": "{the process name}"}
    
If it fails, it should write the following:

    {"success": false, "message": "{error message}"}
    
On error, the server will respond with an `HTTP 500` code. On success, it responds with the usual `HTTP 200`.