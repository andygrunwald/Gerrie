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

Create a new database in your database with name *gerrie* and setup database scheme:

.. code::

    $ ./gerrie gerrie:setup-database --database-host=localhost --database-user=root --database-name=gerrie

Create an account (e.g. *max.mustermann*) in the Gerrit instance you want to crawl (e.g. *review.typo3.org:29418*), add your SSH public key to the Gerrit instance and execute the *gerrie:check* command to check your environment:

.. code::

    $ ./gerrie gerrie:check --database-host=localhost --database-user=root --database-name=gerrie --ssh-key=/Path/To/.ssh/private_key ssh://max.mustermann@review.typo3.org:29418/


If everything is fine start crawling:

.. code::

    $ ./gerrie gerrie:crawl --database-host=localhost --database-user=root --database-name=gerrie --ssh-key=/Path/To/.ssh/private_key ssh://max.mustermann@review.typo3.org:29418/


Now the crawler starts and is doing its job :beer:

You reading can continue in the documentation in the chapters :doc:`Installation</installation/index>`, :doc:`Configuration</configuration/index>`, :doc:`Commands</commands/index>`, :doc:`Database</database/index>` or :doc:`Contributing</contributing/index>`.

.. note::

    Please note that we currently only support SSH and MySQL.
    We are open for changes and contributions. Feel free to push this product forward or get in contact with us.

.. _Open an issue: https://github.com/andygrunwald/Gerrie/issues
