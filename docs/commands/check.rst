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

.. code::

    PHP-Extension "curl" is not installed. Please install PHP-Extension "curl".

it seems to be that the curl extension is not installed or loaded in your environment.
To check if `curl` is installed please list all available PHP modules with

.. code::

    $ php -m

and search for curl.
If this is not in the list please have a detailed look in the `Client URL Library @ PHP.net documentation`_.
Specially the `curl Installation`_ chapter might be useful in your case.

PHP extensions PDO and pdo_mysql
=================================
If you see one of these messages

.. code::

    PHP-Extensions "PDO" and "pdo_mysql" are not installed. Please install both.
    PHP-Extension "PDO" (v1.0.4dev) is installed, but "pdo_mysql" not. Please install it.

it seems to be that the PDO or pdo_mysql extension is not installed or loaded in your environment.
With the following commands you can check if the module(s) are loaded and in which version they are available.

.. code::

    $ php -m
    $ php -r 'var_dump(phpversion("PDO"));'
    $ php -r 'var_dump(phpversion("pdo_mysql"));'

If this leads in a negative check it would make sense to have a look at the `PHP Data Objects @ PHP.net documentation`_.
Specially the `PDO Installation`_ chapter might be useful in your case.
If everything is fine with PDO, but you got problems with pdo_mysql have a look at the `pdo_mysql @ PHP.net documentation`_.

SSH
====
If you see the message

.. code::

    "ssh" is not installed. Please install "ssh".

it seems that the ssh executable can`t be found or used.
SSH must be executable to make use of the SSH API by Gerrit.
If you need more information about SSH please have a look at `OpenSSH`_.

You can test your SSH by

.. code::

    $ ssh -V

Config file location
=====================
The config file location got two different error messages.
If you see the message

.. code::

    Config file "X" was not found.  Please provide the correct path or all settings via command options.

the config file can`t be found. The default value is *Config.yml* of the project root.
You can just copy the *Config.yml.dist* in the same folder and adjust it.
This would fix the problem:

.. code::

    $ cd /path/to/Gerrie
    $ cp Config.yml.dist Config.yml

An alternative would be to apply the *-c* / *--config-file* option.
With this you can put the config file wherever you want.

.. note::

    The configuration file is not required. You can pass all settings by options and arguments.
    If this check fail ensure that you will use the options + arguments.

If you see the message

.. code::

    Config file "X" was found, but is not readable. Please change ownerships or all settings via command options.

then your configuration file was found, but is not readable by the user which executes the Gerrie application.
Please adjust the access rights. Maybe `Chmod`_ and `Chown`_ can help you.

Config file validation
=======================
If you see a message like

.. code::

    The configuration is not complete. Missing keys are X. Please provide them as command options.

the configuration file is not complete.
There are the mentioned settings missing.
If you don`t know which keys need to be in the config file, please have a look at the self documented *Config.yml* in the root directory of Gerrie.

.. note::

    The configuration file is not required. You can pass all settings by options and arguments.
    If this check fail ensure that you will use the options + arguments.


Database connection
====================
If you see a message like

.. code::

    Database connection to host "120.0.0.1" works not as expected. Please check your credentials or setup.

Gerrie can`t build a database connection.
A database connection is required to use Gerrie.
To check if your database is working you can try to connect with the same credentials via commandline:

.. code::

    $ mysql -h 127.0.0.1 -uUSER -p
    $ # enter password here
    $ mysql> USE DATABASENAME;
    $ mysql> SHOW TABLES;

.. note::

    Only MySQL is supported.

Gerrit instance connection
===========================
Depending on your configuration you will use the SSH or HTTP / REST API by Gerrit.
Both connection kinds can fail and will output a error message like

.. code::

    Connection to Gerrit "review.typo3.org" via SSH-DataService was not successful. Please check your credentials or setup.

Please read further to fight against your issue.

Connection via SSH
------
TODO

Connection via HTTP(S)
--------
TODO

.. _Client URL Library @ PHP.net documentation: http://php.net/manual/en/book.curl.php
.. _curl Installation: http://php.net/manual/en/curl.installation.php
.. _PHP Data Objects @ PHP.net documentation: http://php.net/manual/en/book.pdo.php
.. _PDO Installation: http://php.net/manual/en/pdo.installation.php
.. _pdo_mysql @ PHP.net documentation: http://php.net/manual/en/ref.pdo-mysql.php
.. _OpenSSH: http://www.openssh.com/
.. _Chmod: http://en.wikipedia.org/wiki/Chmod
.. _Chown: http://en.wikipedia.org/wiki/Chown