PHP Process
===========

To spawn a PHP process, you must append `?type=php&priority={priority}` to your URL, as the payload
must be comprised entirely of your PHP code.

Also, you **must** specify your `Content-Type` as `application/json`.

An example request:

    GET /add-process?type=php&priority=3
    Host: 127.0.0.1:3440
    Content-Type: application/json
    Accept: */*
    <?php echo 'This is my PHP script!'; ?>
    
Using Guzzle:

    $client = new GuzzleHttp\Client;
    $client->post('http://127.0.0.1:3440?type=php&priority=3', [
        'headers' => ['Content-Type' => 'application/php'],
        'body' => "<?php echo 'This is my PHP script!'; ?>"
    ];
    
While using Guzzle, you can also specify a resource:

    $client->post('http://127.0.0.1:3440?type=php&priority=3', [
        'headers' => ['Content-Type' => 'application/php'],
        'body' => fopen('path/script.php', 'r')
    ];
    
If your script throws an uncaught exception or triggers an error, your process will be marked as `FAILED`.
Otherwise, there is no need to print anything. If it exists with no errors it will be marked as `SUCCESS`.

DISCLAIMER
----------

Please notice that PHP processes are **NOT** sandboxed and thus have full access to your filesystem.
This is a **huge** security risk. Do not allow users to use this, and restrict access to this API to yourself.

This API is supposed to be used for internal process management, not to be available to anyone on the internet.