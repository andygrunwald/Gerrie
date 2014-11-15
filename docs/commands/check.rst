gerrie:check
###############

The `gerrie:check` command is useful to check your local / server environment if everything works fine with your current configuration.
It accepts the same options and arguments as the `gerrie:crawl` command.
With this it is easy to switch the command to check if everything if working.

The `gerrie:check` executes various checks to your environment.
Current checks are:

* if the PHP extension `curl` is installed
* if the PHP extension `PDO` is installed
* if the PHP extension `pdo_mysql`is installed
* if SSH is installed and usable
* if the config file can be found
* if the config file is valid
* if Gerrie can connect to the database
* if Gerrie can connect the configured / passed Gerrit instances

During this checks several errors can occur.
In the next sections we provide possible solution to fix your environment if a check failed.

PHP extension curl
===================
If you see the message

    PHP-Extension "curl" is not installed. Please install PHP-Extension "curl".

it seems to be that the curl extension is not installed or loaded in your environment.
To check if `curl` is installed please list all available PHP modules with

    $ php -m

and search for curl.
If this is not in the list please have a detailed look in the `Client URL Library @ PHP.net documentation`_.
Specially the `curl Installation`_ chapter might be useful in your case.

PHP extensions PDO and pdo_mysql
=================================
TODO

.. code::

    $ php -m
    $ php -r 'var_dump(phpversion("PDO"));'
    $ php -r 'var_dump(phpversion("pdo_mysql"));'

SSH
====
TODO

Config file location
=====================
TODO

Config file validation
=======================
TODO

Database connection
====================
TODO

Gerrit instance connection
===========================
TODO

SSH
------
TODO

HTTP(S)
--------
TODO

.. _Client URL Library @ PHP.net documentation: http://php.net/manual/en/book.curl.php
.. _curl Installation: http://php.net/manual/en/curl.installation.php