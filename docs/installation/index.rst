Installation
###############

Prerequisites
=============

tl;dr
------
* `PHP`_ >= 5.4.0
    * PHP Extension `PDO`_
    * PHP Extension `pdo_mysql`_
    * PHP Extension `curl`_
* `Composer`_
* `MySQL`_ >= v5.1
* SSH

PHP
----
Gerrie is written in `PHP`_.
To use Gerrie the PHP interpreter engine is necessary.
Please install PHP.
The installed version must be **>= 5.4.0**.
See `PHP Download`_ and `PHP Installation and Configuration`_.

If you install PHP from source please ensure that the PHP Extensions `PDO`_, `pdo_mysql`_ and `curl`_ are also available.
This extensions are part of the standard edition. If you install PHP by a standard package manager like apt you are on a save way :)

Composer
--------
Gerrie include several 3rd party libraries.
To manage this dependencies we use the defacto standard tool `Composer`_ for dependency management.
Please install Composer.
In the documentation you can find instructions to install it global or local.
A **specific version is not necessary**.
See `Getting Started at Composer documentation`_.

MySQL
------
Gerrie uses the `MySQL`_ database as storage backend.
Please install MySQL.
The `MySQL Community Edition`_ is completely enough to fit Gerries need.
The installed version must be **>= v5.1**.
See `MySQL Community Downloads`_.

SSH
----
Gerrie make use of the SSH API of Gerrit.
To receive data via SSH the SSH client is necessary.
Please install SSH.
A **specific version is not necessary**.
Most (or every) Linux / Unix distribution got SSH already installed.

Main instructions
==================

Get the source
---------------
You can choose which version of Gerrie you want to install:

* the master branch
* a stable release

The difference between this two versions are:

* The master branch can be unstable, because this is the main development line
* The master branch can be ahead with new features and fixed bugs
* The latest stable release is stable and tested
* The latest stable release can be lack in features

To install the master branch via git just clone the source code:

.. code::

    $ git clone https://github.com/andygrunwald/Gerrie.git Gerrie

An alternative is to download the master branch as zip archive and extract it:

.. code::

    $ wget https://github.com/andygrunwald/Gerrie/archive/master.zip -O Gerrie.zip
    $ unzip Gerrie.zip
    $ mv Gerrie-master Gerrie


Install dependencies
---------------------
Gerrie relies on several 3rd party libraries to speedup the development, make use of proven source code and avoid to reinvent the wheel.

.. code::

    $ cd Gerrie
    $ composer install


Configure the application
----------------------------

*Gerrie* can be configured by a configuration file, options and arguments or both.
The easiest solution is to copy the ``Config.yml.dist`` file to another location and adjust the settings in the self documented configuration file.
This file will be added by the ``--config-file``option.

If you want to learn more about configuration in Gerrie or how to apply options and arguments :doc:`see the Configuration chapter</configuration/index>`.

Execute the gerrie:check command
---------------------------------

Gerrie got a build in command to check if your environment and configuration is working correctly.
This command is named *gerrie:check*.

There are several ways how to execute the check command.
The way depends on your preference.
If you prefer a configuration file which contains all settings (see "Configure the application") then call:

.. code::

    $ ./gerrie gerrie:check --config-file="/Path/To/Config.yml"

If you prefer all settings passed as arguments to Gerrie this will be no problem.
This command accepts many options and arguments.
Get an overview with

.. code::

    $ ./gerrie gerrie:check --help

Here is an example call with using options and arguments instead of an configuration file plus a connection check for the Gerrit instance of the `TYPO3`_ project.

.. code::

    $ ./gerrie gerrie:check --database-host="127.0.0.1" --database-user="gerrie" \
                            --database-pass="secret" --database-port=3306  \
                            --database-name="gerrie"  \
                            --ssh-key="/Users/max/.ssh/id_rsa_gerrie"  \
                            ssh://max@review.typo3.org:29418/

If everything works fine you will see red errors.
If you got one or more errors please have a look at the :doc:`commands *gerrie:check* chapter</commands/check>`.
There you can find a detailed description of the errors and hints how to fix them.

Run Gerrie, run!
---------------------------------

If the *gerrie:check* went well, let Gerrie run.
You have to know *Gerrie* loves crawling Gerrits :)

The main command of *Gerrie* is ``gerrie:crawl``.
Just execute it. It supports the same options and arguments as the ``gerrie:check`` command.

Without configuration file:

.. code::

    $ ./gerrie gerrie:crawl --database-host="127.0.0.1" --database-user="gerrie" \
                            --database-pass="secret" --database-port=3306  \
                            --database-name="gerrie"  \
                            --ssh-key="/Users/max/.ssh/id_rsa_gerrie"  \
                            ssh://max@review.typo3.org:29418/

or with configuration file:

.. code::

    $ ./gerrie gerrie:crawl --config-file="/Path/To/Config.yml"

or with both:

.. code::

    $ ./gerrie gerrie:crawl --config-file="/Path/To/Config.yml" --database-host="127.0.0.1" \
                            --database-user="gerrie" --database-name="gerrie" \
                            ssh://max@review.typo3.org:29418/

.. _PHP: http://php.net/
.. _PHP Download: http://php.net/downloads.php
.. _PHP Installation and Configuration: http://php.net/manual/en/install.php
.. _pdo_mysql: http://php.net/manual/en/ref.pdo-mysql.php
.. _curl: http://php.net/manual/en/book.curl.php
.. _PDO: http://php.net/manual/en/book.pdo.php
.. _Composer: https://getcomposer.org/
.. _Getting Started at Composer documentation: https://getcomposer.org/doc/00-intro.md
.. _MySQL: http://www.mysql.com/
.. _MySQL Community Edition: http://www.mysql.com/products/community/
.. _MySQL Community Downloads: http://dev.mysql.com/downloads/
.. _TYPO3: https://review.typo3.org/