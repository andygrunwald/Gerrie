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
The :doc:`Configuration chapter</configuration/index>` will list all available settings as well.

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
The SSH API is a little bit tricky.

At first the Gerrit instance must support access by SSH.
Instances like `TYPO3`_ or `Wikimedia`_ does this.
Instances like `Android`_ (which are hosted at googlesource) does not.
They only support HTTPS.

One requirement is that you got a user account at this instance and your SSH public key was added in Gerrit at *Settings* > *SSH Public Keys*.
After this you can test your command with

.. code::

    $ ssh -i /Path/To/Your/Private/.ssh/key -p 29418 USERNAME@HOST gerrit version
    # e.g.
    $ ssh -i /Users/max/.ssh/id_rsa_gerrie -p 29418 max.musterman@review.typo3.org gerrit version

A valid response should be

.. code::

    gerrit version 2.9.1

If you see something like "Access denied" please check your private / public key pair.

Connection via HTTP(S)
--------
The HTTP(S) API is a little bit more easier to use than the SSH API.
Mostly every current version of Gerrit supports the REST-API.

.. note::

    The HTTP(S) API is not fully supported by Gerrie.
    This is planned for future versions of Gerrie.

There are two ways to test the REST-API: With and without authentification.
At first be sure that this works without authentification.
This is easy and you can just request a special url with curl like

.. code::

    $ curl SCHEME://HOST/config/server/version
    # e.g.
    $ curl https://review.typo3.org/config/server/version

A valid response should be

.. code::

    )]}'
    "2.9.1"

Next step would be to check the access via REST API with your user credentials.
You can do this via curl as well:

.. code::

    $ curl --user USERNAME:PASSWORD SCHEME://HOST/a/accounts/self/username
    # e.g.
    $ curl --user max.mustermann:mypassword https://review.typo3.org/a/accounts/self/username

A valid response should be

.. code::

    )]}'
    "max.mustermann"

If you got a response like

.. code::

    Unauthorized

please check your username and password at the Gerrit instance.

.. note::

    To crawl a Gerrit instance a authentification is not necessary for the REST-API.
    This depends on your user account.
    For example some instances give logged in users a higher API ratio or more rights to see more projects.

.. _Client URL Library @ PHP.net documentation: http://php.net/manual/en/book.curl.php
.. _curl Installation: http://php.net/manual/en/curl.installation.php
.. _PHP Data Objects @ PHP.net documentation: http://php.net/manual/en/book.pdo.php
.. _PDO Installation: http://php.net/manual/en/pdo.installation.php
.. _pdo_mysql @ PHP.net documentation: http://php.net/manual/en/ref.pdo-mysql.php
.. _OpenSSH: http://www.openssh.com/
.. _Chmod: http://en.wikipedia.org/wiki/Chmod
.. _Chown: http://en.wikipedia.org/wiki/Chown
.. _Android: https://android-review.googlesource.com/
.. _TYPO3: https://review.typo3.org/
.. _Wikimedia: https://gerrit.wikimedia.org