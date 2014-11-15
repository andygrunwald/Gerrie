gerrie:crawl
###############

The `gerrie:crawl` command is the main command of the *Gerrie* application.
The main responsibility of this command is to receive the data from the Gerrit instance and transfer the data into a RDBMS.
This happens in several steps:

#. Receive all information of configuration / options + arguments
#. Query the Gerrit instance for the / a project(s)
#. Transfer the project data into a unique format
#. Proceed (insert / update) project information
#. Query the Gerrit instance for Changesets + detailed information
#. Transfer the Changeset data into a unique format
#. Proceed (insert / update) changeset information

One requirement to execute `gerrie:crawl` is the necessary database structure.
If this structure is not there the command `gerrie:create-database` will be executed before `gerrie:crawl` starts with its main logic.

.. note::

    At the moment Gerrie is only able to communicate with the SSH API of Gerrie.
    The support for the REST API is not build in yet in Gerrie.

Next to the described main logic there a several small features build in like

* debugging functionality to detect if every attribute which is received by the API is transformed to the unique format and no information is missing
* debugging functionality to detect if the project / instance is crawled the first time and only insert statements and not update statements will be executed
* logging to see what happens
