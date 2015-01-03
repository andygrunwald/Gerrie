gerrie:list-projects
########################

The `gerrie:list-projects` command is responsible to list all projects of the given Gerrit instances.
The `gerrie:list-projects` command won`t inserts or updates anything in the storage backend.
It only requests the API of the given Gerrit instances.

Example
===================

If we configure two different Gerrit instances, `TYPO3`_ via SSH and `Wikimedia`_ via HTTP (Config.yml) ...

.. code::

    ...
    Gerrit:
        TYPO3:
            - ssh://max.mustermann@review.typo3.org:29418/

    WikiMedia:
        - https://max.mustermann:password@gerrit.wikimedia.org/r/

... and start *Gerrie* to receive the projects ...

.. code::

    $ ./gerrie gerrie:list-projects -c Config.yml


... we will get all projects of the two Gerrit instances ...

.. code::

    Instance: review.typo3.org (via SSH)
    ========================================
    All-Projects
    CalBrowser
    Documentation/ApiTypo3Org
    Documentation/GetTheDocs
    Documentation/Manuals
    Documentation/RestTools
    Documentation/Sandbox
    Documentation/TYPO3/Book/ExtbaseFluid
    ...

    Instance: gerrit.wikimedia.org (via HTTP)
    ========================================
    All-Projects
    USERINFO
    VisualEditor
    VisualEditor/VisualEditor
    analytics
    analytics/abacist
    analytics/aggregator
    analytics/aggregator/data
    ...

Use cases
===================

Use cases for this features are:

* Get a simple overview about the projects which can be crawled by the given user you had configured
* Combine the project listing with commands like `grep` / `awk` / `sed` and *Gerrie* (again) to crawl all projects with a given pattern
* Execute the :doc:`gerrie:crawl</commands/crawl>` command in parallel (multi processes / threading) to gain more speed during cralwing

.. note::

    You got another use case?
    Please let us know and open an issue in our bugtracker.
    How you do this can be read in the :doc:`contributing/issues chapter</contributing/issues>`.
    We will add your usecase here.

.. _TYPO3: https://typo3.org/
.. _Wikimedia: https://www.wikimedia.org/