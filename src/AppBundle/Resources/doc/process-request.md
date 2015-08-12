The Request Process
===================

The request process type is meant to help dispatch a HTTP request to some webpage. Besides the usual
`type` and `priority`, one has to specify the following parameters:

**url** (required):

The URL to be accessed.

**method** (required):

The HTTP verb to use on the request (GET|POST|PUT|DELETE|OPTION|PATCH|etc).

**headers** (optional):

The headers, as an array. Example:

    $headers = ['Content-Type' => 'application/json'];
    
**query** (required|optional):

This argument is a querystring, in the format `variable=value&anothervariable=anothervalue`. This is required
for POST requests without a payload, but is optional for all other verbs.

**payload** (optional):

This argument is the raw request body. This is meant to help with requests that contain either a JSON or a XML in
its payload.

---

A sample process:

    $client = new GuzzleHttp\Client;
    $client->post('http://127.0.0.1:3440', [
        'form_params' => [
            'type' => 'request',
            'priority' => 3,
            'url' => 'http://mywebsite.com'
            'method' => 'POST',
            'headers' => serialize(['Content-Type' => 'application/json']),
            'payload' => json_encode(['My Variable' => 'My value'])
        ]
    ]);
    
The snippet above will enqueue a process that when dispatched will create an HTTP POST request for the URL
`http://mywebsite.com`, with the header `Content-Type: application/json` and `{"My Variable":"My value"}` in
the content payload (the raw body).

However, if I want to make a regular POST request with form data, I can do:

    $client = new GuzzleHttp\Client;
    $client->post('http://127.0.0.1:3440', [
        'form_params' => [
            'type' => 'request',
            'priority' => 3,
            'url' => 'http://mywebsite.com'
            'method' => 'POST',
            'query' => 'myvariable=myvalue'
        ]
    ]);
    
The process result will be marked with `SUCCESS` with the target URL responds with an `HTTP 2xx` or with `HTTP 3xx`.
However, if the server responds with an `HTTP 4xx` or `HTTP 5xx`, it will be marked with `FAILED`.