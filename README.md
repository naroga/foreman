Foreman
=======

Naroga/Foreman is a worker manager. It allows you to spawn parallel processes without spawning *too many* 
processes at once (and thus consuming all your resources). It's an easy way to enqueue new processes, sit back and
wait to be notified when the process is done.

It's easy to configure and has a built-in queue priority schema.

Project requisites
------------------

This project is now supported in all major OS': UNIX, OSX and Windows.

It requires PHP 5.5+ (or 7.0+), curl, json, and xml extensions.

See the [Symfony Requirements](http://symfony.com/doc/current/reference/requirements.html)
for more specific and in-depth requirements.

You can check if your system is ready to use by running `php app/check.php`.

Installation
------------

Use [Composer](https://getcomposer.org) to install this project and all its dependencies:

    composer create-project naroga/foreman
    
The installation wizard will ask you for a few parameters, such as a hostname and a port for the 
daemon service. You can change these, but if you wish, you can just press ENTER at each
request to use the default configuration, which should run just fine.
    
Configuration
-------------

**1. Setting up the servers**

There are two servers in this project that should be running at all times

**1.1. The Daemon Service**

The demon is started from the command line, and if you installed this application using
`composer` (see the topic above), it should be good to go. Just check if your firewall
isn't blocking connections in the specified port (defaults to `3440`). If you wish to make
changes to the host/port, you should edit `app/config/parameters.yml`.

**1.2. The http server**

The http server runs like you would expect from a PHP project. Just set up a virtual host pointing
to the /web/ folder. If you wish to troubleshoot or to get more information on setting up the virtual host,
you should refer to the 
[Symfony WebServer configuration](http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html) 
documentation.

**2. Additional configuration**

Naroga/Foreman comes preconfigured, so you can skip this section. If you wish to 
tweak the configuration to improve responsiveness, resource usage or to change the default behaviour, proceed to the 
[configuration reference](/src/AppBundle/Resources/doc/configuration.md).

Usage
-----

To get started on using this project, proceed to the [documentation](/src/AppBundle/Resources/doc/index.md).

License
-------

This project is released under the MIT License. For more information, see the [LICENSE](/LICENSE) file.
