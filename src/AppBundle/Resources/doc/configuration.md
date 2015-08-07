Configuration reference
=======================

Before getting started on using the Foreman Processor, you might want to tweak the Processor configuration
in order to improve responsiveness or to save some of your precious server resources.

Host and Port
-------------

The Processor host configuration is requested when installing the project, through the `composer create-project`
command. If you want to change your configuration later on, they are available at `app/config/parameters.yml`.

    parameters:
        ...
        foreman.processor.protocol: http
        foreman.processor.port: 3440
        foreman.processor.host: 127.0.0.1
        
---

If you want to change the application behaviour, you should change the service parameters. These
are located in `app/config/services.yml`, under the section `parameters`, in the key `foreman.processor`, 
as shown below:

    parameters:
        foreman.processor:
            #HERE ARE THE VALUES YOU WANT TO CHANGE.

Here is a comprehensive list of all available keys, what they mean and when your should mess around with them.


interval
--------

Describes the amount of time, in seconds, between two interactions. The lower the value, the more responsive your
application will be, but the more resources (CPU) it will use. The processor waits for this amount of time before
checking if there are new queued processes or if the currently dispatched processes finished (or timed out).

Do *not* set this value to 0 (zero).

**Type**: integer

**Default**: 0.5

---

Proceed to the [Foreman:Processor Server usage reference](server.md).