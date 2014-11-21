Writing Documentation
#######################

Documentation is really important.
Especially for an Open Source project where no one is payed to work with (maybe legacy) software.
Documentation helps user
* to understand how to use software
* what are basic concepts of the tool
* to follow the original thoughts of the author
* to start contributing to the tool
* understand how they can be involved
* and many things more.

This are the reasons why documentation is important.
We at Gerrie thinks that to write documentation must be easy.
That is the reason why we store documentation as **plain text next to the source code**.
You can find our documentation in the `docs/`_ directory.

Learn more about how we write and where we host and generate our documentation.

reStructuredText
==================
We write our documentation in `reStructuredText`_:

    reStructuredText is a file format for textual data used primarily [..] for technical documentation.

reStructuredText is very similar to `MarkDown`_. ReStructuredText is easy to write and really powerful.
You can convert this to several formats like HTML, ePub or PDF.

To learn the reStructuredText syntax have a look at `reStructuredText @ Wikipedia`_ or `Quick reStructuredText of docutils`_.

Read the Docs
==================
We use the service of `Read the docs`_ for hosting and generating our documentation.
Read the Docs makes it really easy to write, build and manage documentations of Open Source projects.
The maintenance is less and the author can focus on the relevant topics instead of maintaining infrastructure.

To start and use Read the Docs and to compile and test the documentation you have to install a few Python tools.
For example Read the Docs uses `Sphinx`_ to render the documentation.
Details about the necessary software can be found in the `Getting started @ Read the docs`_.

To render our documentation just checkout the source code from GitHub, compile the documentation and open it:

.. code::

    $ git clone https://github.com/andygrunwald/Gerrie.git Gerrie
    $ cd Gerrie/docs
    $ make html
    $ open _build/html/index.html

After the new docs are pushed to the main Repository of GitHub, Read the Docs will render the new version and publish it to `Gerrie @ Read the Docs`_.

.. _docs/: https://github.com/andygrunwald/Gerrie/tree/master/docs
.. _reStructuredText: http://en.wikipedia.org/wiki/ReStructuredText
.. _MarkDown: http://en.wikipedia.org/wiki/Markdown
.. _reStructuredText @ Wikipedia: http://en.wikipedia.org/wiki/ReStructuredText
.. _Quick reStructuredText of docutils: http://docutils.sourceforge.net/docs/user/rst/quickref.html
.. _Read the docs: https://readthedocs.org/
.. _Getting started @ Read the docs: https://read-the-docs.readthedocs.org/en/latest/getting_started.html
.. _Sphinx: http://sphinx-doc.org/
.. _Gerrie @ Read the Docs: https://gerrie.readthedocs.org/