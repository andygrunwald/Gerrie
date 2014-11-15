Writing Code
###############

General
=======

Coding Style Guide
------------------

For convenience we follow the `PSR-1`_ and `PSR-2`_ coding style guides of `PHP Framework Interop Group`_.
Please be so nice to take care of this during code contribution (e.g. pull requests).
To check your code against this standards you can use tools like `PHP_CodeSniffer`_.

.. _PSR-1: http://www.php-fig.org/psr/psr-1/
.. _PSR-2: http://www.php-fig.org/psr/psr-2/
.. _PHP Framework Interop Group: http://www.php-fig.org/
.. _PHP_CodeSniffer: https://github.com/squizlabs/PHP_CodeSniffer/

Pull requests
=============

With Gerrit we follow the standard code contribution of the GitHub platform.
This means:

1. Fork the project into a personal username space and clone the repository.
    .. code::

        $ git clone https://github.com/andygrunwald/Gerrie.git

2. Create a new git branch for your change (bugfix, feature, improvement, etc.).
    .. code::

        $ git checkout -b my-new-feature

3. Make your changes in the codebase until your changes are working.
    .. code::

        $ vim ./file
        $ # Hack hack hack

4. Commit your changes into your local git repository.
    .. code::

        $ git commit -am 'Add some feature'

5. Push your new branch to your fork repository.
    .. code::

        $ git push origin my-new-feature

6. Visit the forked repository via the GitHub website and create the pull request based on your new branch.
    .. code::

        $ # Ploepp (beer open)
        $ # gluck gluck gluck (beer drinking)

This are the necessary steps described in a really rough way.
If you need more help the GitHub help pages are a a excellent source:

* `Fork A Repo`_
* `Creating a pull request`_
* `Using pull requests`_
* `Syncing a fork`_
* `Merging an upstream repository into your fork`_
* `Configuring a remote for a fork`_


Testing
=======

PHPUnit
-------

*Gerrie* uses `PHPUnit`_ to create unit and integration tests.
The tests are located in the ``tests`` folder.
To execute the unit tests ensure that you have installed all development dependencies via ``--dev`` and start PHPUnit:

.. code::

    $ composer install --dev
    $ # Without code coverage generation
    $ ./vendor/bin/phpunit --coverage-clover=coverage.clover
    $ # With code coverage generation
    $ ./vendor/bin/phpunit

.. note::

    To generate the code coverage you need the PHP Extension ``xDebug`` installed.

To create mock objects we use the standard functionality of PHPUnit.

Quality services
=================

Travis CI
---------

`Travis CI`_ is a free hosted Continuous Integration Platform for Open Source projects.
We make us of this service to execute our tests continuous.
One of the biggest advantages is that all pull request will be checked with Travis CI as well.
So if you want to contribute please do not fear to break something.
Every pull request you create will be checked and you will be notified if something go wrong.
So just try it :)

See `andygrunwald/Gerrie @ Travis CI`_.

Scrutinizer
-----------

`Scrutinizer`_ is a free hosted Continuous inspection Platform for Open Source projects.
This service executes several checks for us like
* checking the coding styleguide for us
* observe the code documentation about possible bugs in return values
* determine a quality score for *Gerrie*
* and adds small tipps of how to improve the code quality of the software

As a small additional feature we push the generated code coverage from our unit tests from Travis CI to Scrutinizer.
With this Scrutinizer can determine the overall code coverage for us.

See `andygrunwald/Gerrie @ Scrutinizer`_.

.. _Fork A Repo: https://help.github.com/articles/fork-a-repo
.. _Creating a pull request: https://help.github.com/articles/creating-a-pull-request/
.. _Using pull requests: https://help.github.com/articles/using-pull-requests/
.. _Syncing a fork: https://help.github.com/articles/syncing-a-fork/
.. _Merging an upstream repository into your fork: https://help.github.com/articles/merging-an-upstream-repository-into-your-fork/
.. _Configuring a remote for a fork: https://help.github.com/articles/configuring-a-remote-for-a-fork/
.. _Travis CI: https://travis-ci.org/
.. _andygrunwald/Gerrie @ Travis CI: https://travis-ci.org/andygrunwald/Gerrie
.. _Scrutinizer: https://scrutinizer-ci.com/
.. _andygrunwald/Gerrie @ Scrutinizer: https://scrutinizer-ci.com/g/andygrunwald/Gerrie/
.. _PHPUnit: https://phpunit.de/