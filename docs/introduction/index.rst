Introduction
###############

`Gerrie`_ is a data and information crawler for `Gerrit`, a code review system developed by Google.

*Gerrie* uses the SSH and REST-APIs offered by *Gerrit* to transform the data from Gerrit into a RDBMS. Currently only MySQL is supported.
After the transformation the data can be used to start simple queries or complex analysis. One usecase is to analyze communites which use *Gerrit* like `TYPO3`_, `Wikimedia`_, `Android`_, `Qt`_, `Eclipse`_ and `many more`_.

* Website: `andygrunwald.github.io/Gerrie`_
* Source code: `Gerrie @ GitHub`_
* Documentation: `Gerrie @ Read the Docs`_

Features
=========

* Full imports
* Incremental imports
* Full support of SSH-API
* Command line interface
* MySQL as storage backend
* Debugging functionality
* Logging functionality
* Full documented

Read further
=============
If you want to get start really quick please have a look at the :doc:`getting started guide</getting_started/index>`.

When you want to run this in a more proven environment please have a look at the :doc:`installation</installation/index>` and :doc:`configuration</configuration/index>` chapter.

The :doc:`commands section</commands/index>` will explain the functionality of all commands implemented in Gerrie.

As a business analyst / data science engineer / math or numbers lover you can retrieve data and get an understanding of the database structure in the :doc:`database chapter</database/index>`.

You want to contribute? Great! Get more information in the :doc:`contribution chapter</contributing/index>`.

.. _Gerrie: https://andygrunwald.github.io/Gerrie/
.. _Gerrit: https://code.google.com/p/gerrit/
.. _TYPO3: https://review.typo3.org/
.. _Wikimedia: https://gerrit.wikimedia.org/
.. _Android: https://android-review.googlesource.com/
.. _Qt: https://codereview.qt-project.org/
.. _Eclipse: https://git.eclipse.org/r/
.. _many more: http://en.wikipedia.org/wiki/Gerrit_(software)#Notable_users
.. _andygrunwald.github.io/Gerrie: https://andygrunwald.github.io/Gerrie/
.. _Gerrie @ Read the Docs: https://gerrie.readthedocs.org/en/latest/
.. _Gerrie @ GitHub: https://github.com/andygrunwald/Gerrie