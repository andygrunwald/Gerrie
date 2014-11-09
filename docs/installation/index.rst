Installation
###############

Prerequisites
=============

PHP
----
Gerrie is written in `PHP`_.
To use Gerrie the PHP interpreter engine is necessary.

Please install PHP.
The installed version must be **>= 5.4.0**.

See `PHP Download`_ and `PHP Installation and Configuration`_.

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

TODO

Execute the gerrie:check command
---------------------------------

TODO

.. _PHP: http://php.net/
.. _PHP Download: http://php.net/downloads.php
.. _PHP Installation and Configuration: http://php.net/manual/en/install.php
.. _Composer: https://getcomposer.org/
.. _Getting Started at Composer documentation: https://getcomposer.org/doc/00-intro.md
.. _MySQL: http://www.mysql.com/
.. _MySQL Community Edition: http://www.mysql.com/products/community/
.. _MySQL Community Downloads: http://dev.mysql.com/downloads/