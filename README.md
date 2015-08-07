Foreman
=======

Naroga/Foreman is a worker manager. It allows you to asynchronously spawn parallel processes
without relying on `pthreads`, without spawning *too many* processes at once (and thus consuming
all your resources). It's an easy way to enqueue new processes, sit back and
wait to be notified when the process is done.

It's easy to configure and has a built-in queue priority schema.

Installation
------------

Use [Composer](https://getcomposer.org) to install this project and all its dependencies:

    composer create-project naroga/foreman -s dev
    
Configuration
-------------

Naroga/Foreman comes preconfigured, so you can skip this section. If you wish to 
tweak the configuration to improve responsiveness, resource usage or to change 
the default host/port of the application, proceed to the 
[configuration reference](/src/AppBundle/Resources/doc/configuration.md).

Otherwise, you can skip this section altogether.

Usage
-----

To get started on using this project, proceed to the [documentation](/src/AppBundle/Resources/doc/index.md).

License
-------

This project is released under the MIT License. For more information, see the [LICENSE file](/LICENSE).