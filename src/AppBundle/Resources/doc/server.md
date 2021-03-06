The Foreman:Processor Server
============================

This is probably the most important part of your application. The Foreman:Processor server is
responsible for managing your workers, starting queued processes, killing timed out processes and 
notifying your application that a specific process has finished. If it's not running, you won't be
able to add processes to the queue.

**INFORMATION:**

If you are already familiar with the Symfony Console, you should know that the default
environment has been switched from 'dev' to 'prod' in this project. This means that
by running the commands in this documentation, you are running the kernel in 'production'
mode. To use the default 'development' environment, append `--env=dev` to the commands.
Also, if you make changes to the code, you will need to clear the production cache.

    php app/console cache:clear
    
No need to use `--env=prod`, as this is the default environment. 

Starting the server
-------------------

The server should be started from the command line, from the Symfony Console application. Follow the example below:

    php app/console foreman:processor:start
    
The process should now be up and running. It's that easy.

By default, your server listens on `127.0.0.1:3440`, so you might want to unblock that port in your firewall. If you
wish to change these parameters, check the [configuration reference](configuration.md#host-and-port).

There are two very useful additional options for your server, as described below.

**1. Daemon mode**

You probably want to close your terminal/console window and not kill the Foreman:Processor server.
Or maybe you want to start it on the OS startup. You can achieve both by running the server in the background,
appending the `--daemon` (or simply `-d`) option to the command line:

    $ php app/console foreman:processor:start -d
    The server has been started successfully with PID <pid>
    
To confirm that the process is running, you can check your process list:

    $ ps a | grep foreman
    <pid> tty1     S      0:00 /usr/bin/php5 app/console foreman:processor:start
    
**2. Verbose mode**

If you wish to see more information while running the server (such as the symfony kernel information and 
application warnings), you can append `--verbose` (or simply `-v`) to the command line.

    $ php app/console foreman:processor:start -v
    
Obviously, you can use both options simultaneously, by appending `-d -v` (don't mind the option order) to the command 
line.

Stopping the server
-------------------

Now that your Foreman:Processor server is up and running, you can stop it whenever you want. There are mainly two ways 
to do so.

**1. Command line**

The command below is going to send a `SIGTERM` message to the server.

    $ php app/console foreman:processor:stop
    A `SIGTERM` has been sent to the Foreman Processor.
 
**2. Http request**
 
You can also manage your Foreman:Processor server using the restful API. Just create an HTTP GET request for the /stop 
URI, and it should work.

    $ curl -X GET http://127.0.0.1:3440/stop
    {"success":true,"message":"SIGTERM Received"}
    
Just keep in mind this host is protected and will only allow requests made from the same machine (127.0.0.1).

Previous: [Configuration reference](configuration.md); Next step: [Spawning processes](process.md)