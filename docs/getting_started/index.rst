Getting Started
###############

This is a quick getting started guide.
It will helps you to get the first try of Gerrie up and running.

.. note::

    If you encounter errors or bugs during the getting started guide, don`t give up!
    `Open an issue`_ or read about the details in the :doc:`Installation</installation/index>`, :doc:`Configuration</configuration/index>`, :doc:`Commands</commands/index>` or :doc:`Database</database/index>` chapter.

Download application and install dependencies:

.. code::

    $ git clone https://github.com/andygrunwald/Gerrie.git .
    $ composer install

Copy config file and adjust configuration (*Database*, *SSH*, *Gerrit*):

.. code::

    $ cp Config.yml.dist Config.yml
    $ vim Config.yml

A minimalistic configuration for the *TYPO3 Gerrit instance* with the user *max.mustermann* can look like:

.. code::yaml

    Database:
      Host: 127.0.0.1
      Username: root
      Password:
      Port: 3306
      Name: gerrie

    SSH:
      KeyFile: /Users/max/.ssh/id_rsa_gerrie

    Gerrit:
      TYPO3:
        - ssh://max.mustermann@review.typo3.org:29418/

Create a new database in your database with name *gerrie* and setup database scheme:

.. code::

    $ mysql -u root -e "CREATE DATABASE gerrie;"
    $ ./gerrie gerrie:setup-database --config-file="./Config.yml"

Create an account (e.g. *max.mustermann*) in the Gerrit instance you want to crawl (e.g. *review.typo3.org:29418*), add your SSH public key to the Gerrit instance and execute the *gerrie:check* command to check your environment:

.. code::

    $ ./gerrie gerrie:check --config-file="./Config.yml"

.. note::

    **Important:**
    If your SSH key is protected by a passphrase this check will ask you to enter your passphrase to use the private key for this connection.
    Gerrie does not save or transfer this passphrase to any foreign server.
    The private key is only necessary to authenticate against the Gerrit instance.

If everything is fine start crawling:

.. code::

    ./gerrie gerrie:crawl --config-file="./Config.yml"


Now the crawler starts and is doing its job :beer:

You reading can continue in the documentation in the chapters :doc:`Installation</installation/index>`, :doc:`Configuration</configuration/index>`, :doc:`Commands</commands/index>`, :doc:`Database</database/index>` or :doc:`Contributing</contributing/index>`.

.. note::

    Please note that we currently only support SSH and MySQL.
    We are open for changes and contributions. Feel free to push this product forward or get in contact with us.

.. _Open an issue: https://github.com/andygrunwald/Gerrie/issues