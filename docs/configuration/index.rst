Configuration
###############

*Gerrie* supports two ways to pass a configuration in: **Options + Arguments** and **configuration file**.
There is no need for a configuration file.
All settings can be passed by options and arguments.

Advantage of using a configuration file:

* Shorter commands
* Simple versioning of configuration file

Advantage of using options + arguments:

* More flexible, because you don`t need to modify a file

Next to the 0 and 1 solution you can combine both worlds.
You can add a configuration file by option *--config-file* and overwrite attributes from the configuration file by options added to the command.
The options got a **higher** priority as the attributes from the configuration file.
The arguments will be **merged** with the Gerrit instances configured in the configuration file.

Example configuration file:

.. code:: yaml

    Database:
        Host: 127.0.0.1
        Username: root
        Password:
        Port: 3306
        Name: gerrit

    SSH:
        KeyFile: /Users/agrunwald/.ssh/id_rsa_gerrie

    Gerrit:
        TYPO3:
            - ssh://max@review.typo3.org:29418/
            - { Instance: ssh://max.mustermann@review.typo3.org:29418/, KeyFile: /Users/max/.ssh/id_rsa_local }

Example command:

.. code::

    $ ./gerrie gerrie:crawl --config-file=/path/to/example-conf.yml \
                            --database-host="192.168.1.10" -u="operator" \
                            https://max.mustermann:password@gerrit.wikimedia.org/ \
                            https://max:secret@android-review.googlesource.com/

In this example Gerrie will use:

* Database hostname: *192.168.1.10*
* Database username: *operator*
* Database password:
* Database port: *3306*
* Database name: *gerrit*
* ...
* Instances:
    * ssh://max@review.typo3.org:29418/
    * ssh://max.mustermann@review.typo3.org:29418/
    * https://max.mustermann:password@gerrit.wikimedia.org/
    * https://max:secret@android-review.googlesource.com/

Options
========
*Gerrie* supports a several options.
Options are parameters prefixed by *--* or *-*.
Example are in the long variant *--help* or in the short variant *-h*.
Options can be accept a value (like *--config-file="..."*) or are standalone (like *--version*).

.. note::

    Please have a look at the command you want to use first which options are supported.
    **Not all options are supported by all commands**.
    You can list options by command by *./gerrie gerrie:YOUR-COMMAND --help*.
    For available commands execute *./gerrie*.

Options will by added to the command like
.. code::

    $ ./gerrie gerrie:check --option1 --option2=value ...

Here you can find a list of all supported options.

+-----------------+----------------+----------------------------------------------------------------------------+
| Long option     | Short option   | Description                                                                |
+=================+================+============================================================================+
| --config-file   | -c             | Path to configuration file (default: "Config.yml") (TODO)                  |
+-----------------+----------------+----------------------------------------------------------------------------+
| --database-host | -H             | Name / IP of the host where the database is running (TODO)                 |
+-----------------+----------------+----------------------------------------------------------------------------+
| --database-user | -u             | Username to access the database (TODO)                                     |
+-----------------+----------------+----------------------------------------------------------------------------+
| --database-pass | -p             | Password to access the database (TODO)                                     |
+-----------------+----------------+----------------------------------------------------------------------------+
| --database-port | -P             | Port where the database is listen (TODO)                                   |
+-----------------+----------------+----------------------------------------------------------------------------+
| --database-name | -N             | Name of the database which should be used (TODO)                           |
+-----------------+----------------+----------------------------------------------------------------------------+
| --ssh-key       | -k             | Path to SSH private key for authentication (TODO)                          |
+-----------------+----------------+----------------------------------------------------------------------------+
| --help          | -h             | Display this help message. (TODO)                                          |
+-----------------+----------------+----------------------------------------------------------------------------+

Arguments
==========
Next to options *Gerrie* supports arguments.
Arguments are added at the end of the command separated by whitespace.

.. note::

    Please have a look at the command you want to use first which arguments are supported.
    **Not all arguments are supported by all commands**.
    You can list options by command by *./gerrie gerrie:YOUR-COMMAND --help*.
    For available commands execute *./gerrie*.

Here you can find a list of all supported arguments.

+--------------+--------------------------------------------------------------------------------------------+
| Argument     | Description                                                                                |
+==============+============================================================================================+
| instances    | List of instances to crawl separated by whitespace.                                        |
|              | Format scheme://username[:password]@host[:port]/ (TODO)                                    |
+--------------+--------------------------------------------------------------------------------------------+


Configuration file
======================

The configuration file can be used to avoid long options and arguments.
It can be located on the harddisk where *Gerrie* runs.
The format of the configuration file is `YAML`_.
Ensure that you write the correct YAML syntax.
YAML can be a little bit tricky when it comes to intention.

.. note::

    In the root of *Gerrie* there is a *Config.yml.dist* which can be copied and used as a template for your configuration file.

If a attribute contains a "." this means that it will be a nested attribute.
E.g. The attributea *Database.Host* and *Database.Username* will be in configuration file

.. code:: yaml

    Database:
        Host: 127.0.0.1
        Username: root

Here you can find a list of all supported configuration settings.

+-------------------+--------------------------------------------------------------------------------------------+
| Attribute         | Description                                                                                |
+===================+============================================================================================+
| Database.Host     | List of instances to crawl separated by whitespace.                                        |
+-------------------+--------------------------------------------------------------------------------------------+
| Database.Username | List of instances to crawl separated by whitespace.                                        |
+-------------------+--------------------------------------------------------------------------------------------+
| Database.Password | List of instances to crawl separated by whitespace.                                        |
+-------------------+--------------------------------------------------------------------------------------------+
| Database.Port     | List of instances to crawl separated by whitespace.                                        |
+-------------------+--------------------------------------------------------------------------------------------+
| Database.Name     | List of instances to crawl separated by whitespace.                                        |
+-------------------+--------------------------------------------------------------------------------------------+
| SSH.KeyFile       | List of instances to crawl separated by whitespace.                                        |
+-------------------+--------------------------------------------------------------------------------------------+
| Gerrit.Name1      | List of instances to crawl separated by whitespace.                                        |
+-------------------+--------------------------------------------------------------------------------------------+
| Gerrit.Name1.0    | List of instances to crawl separated by whitespace.                                        |
+-------------------+--------------------------------------------------------------------------------------------+
| Gerrit.Name1.N    | | List of instances to crawl separated by whitespace.                                      |
|                   | | List of instances to crawl separated by whitespace.                                      |
+-------------------+--------------------------------------------------------------------------------------------+
| Gerrit.NameN      | List of instances to crawl separated by whitespace.                                        |
+-------------------+--------------------------------------------------------------------------------------------+

.. _YAML: http://en.wikipedia.org/wiki/YAML