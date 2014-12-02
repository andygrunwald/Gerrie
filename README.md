# Gerrie

[![Build Status](https://secure.travis-ci.org/andygrunwald/Gerrie.png)](http://travis-ci.org/andygrunwald/Gerrie)
[![Dependency Status](https://www.versioneye.com/user/projects/53554e47fe0d078a76000002/badge.png)](https://www.versioneye.com/user/projects/53554e47fe0d078a76000002)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/andygrunwald/Gerrie/badges/quality-score.png?s=8f10d347018a564f6dacc1b8a796f0150941691b)](https://scrutinizer-ci.com/g/andygrunwald/Gerrie/)
[![Code Coverage](https://scrutinizer-ci.com/g/andygrunwald/Gerrie/badges/coverage.png?s=ab1ccbb607ee2d00f97c32e87b7395ef5d6daa72)](https://scrutinizer-ci.com/g/andygrunwald/Gerrie/)
[![Documentation Status](https://readthedocs.org/projects/gerrie/badge/?version=latest)](https://readthedocs.org/projects/gerrie/?badge=latest)

*[Gerrie](https://andygrunwald.github.io/Gerrie/)* is a data and information crawler for *[Gerrit](https://code.google.com/p/gerrit/)*, a code review system developed by Google.

*Gerrie* uses the SSH and REST-APIs offered by *Gerrit* to transform the data from Gerrit into a RDBMS. Currently only MySQL is supported.
After the transformation the data can be used to start simple queries or complex analysis. One usecase is to analyze communites which use *Gerrit* like [TYPO3](https://review.typo3.org/), [Wikimedia](https://gerrit.wikimedia.org/), [Android](https://android-review.googlesource.com/), [Qt](https://codereview.qt-project.org/), [Eclipse](https://git.eclipse.org/r/) and [many more](http://en.wikipedia.org/wiki/Gerrit_(software)#Notable_users).

* Website: [andygrunwald.github.io/Gerrie](https://andygrunwald.github.io/Gerrie/)
* Source code: [Gerrie @ GitHub](https://github.com/andygrunwald/Gerrie)
* Documentation: [*Gerrie* @ Read the Docs](https://gerrie.readthedocs.org/en/latest/)

## Features

* Full imports
* Incremental imports
* Full support of SSH-API
* Command line interface
* MySQL as storage backend
* Debugging functionality
* Logging functionality
* Full documented

## Getting started

Download application and install dependencies:
```
$ git clone https://github.com/andygrunwald/Gerrie.git .
$ composer install
```

Create a new database in your database with name *gerrie* and setup database scheme:
```
$ ./gerrie gerrie:setup-database --database-host=localhost --database-user=root --database-name=gerrie
```

Create an account (e.g. *max.mustermann*) in the Gerrit instance you want to crawl (e.g. *review.typo3.org:29418*), add your SSH public key to the Gerrit instance and execute the *gerrie:check* command to check your environment:
```
$ ./gerrie gerrie:check --database-host=localhost --database-user=root --database-name=gerrie --ssh-key=/Path/To/.ssh/private_key ssh://max.mustermann@review.typo3.org:29418/
```

**Important:**
If your SSH key is protected by a passphrase this check will ask you to enter your passphrase to use the private key for this connection.
Gerrie does not save or transfer this passphrase to any foreign server.
The private key is only necessary to authenticate against the Gerrit instance.

If everything is fine start crawling:
```
$ ./gerrie gerrie:crawl --database-host=localhost --database-user=root --database-name=gerrie --ssh-key=/Path/To/.ssh/private_key ssh://max.mustermann@review.typo3.org:29418/
```

Now the crawler starts and is doing its job :beer:

You reading can continue in the documentation in the chapters [Installation](https://gerrie.readthedocs.org/en/latest/installation/index.html), [Configuration](https://gerrie.readthedocs.org/en/latest/configuration/index.html), [Commands](https://gerrie.readthedocs.org/en/latest/commands/index.html), [Database](https://gerrie.readthedocs.org/en/latest/database/index.html) or [Contributing](https://gerrie.readthedocs.org/en/latest/contributing/index.html).

## Documentation

The complete and detailed documentation can be found at [*Gerrie* @ Read the Docs](https://gerrie.readthedocs.org/en/latest/).
The documentation is written in [reStructuredText](http://en.wikipedia.org/wiki/ReStructuredText) and shipped with the source code and can be found in the [*docs/*](https://github.com/andygrunwald/Gerrie/tree/master/docs) folder.

## Source code

The source code can be found at [*andygrunwald/Gerrie @ GitHub*](https://github.com/andygrunwald/Gerrie).

## Contributing

Contribution is welcome at every time.

Contribution is not limited to source code. Also documentation, issues (bugs, new features, nice improvements), talks at usergroups or conferences and so on.
In our documentation you can find more detailed information about contribution.

See [*Gerrie: Contribution* @ Read the Docs](http://gerrie.readthedocs.org/en/latest/contributing/).

## License

This project is released under the terms of the [MIT license](http://en.wikipedia.org/wiki/MIT_License).

## Support, contact or feedback

If you got questions, got feedback, getting crazy with setting up or using this project or want to drink a :beer: and talk about this project **just contact me**.

Write me an email (see [*Andy @ GitHub*](https://github.com/andygrunwald)) or tweet me ([@andygrunwald](http://twitter.com/andygrunwald)).
And of course, you can just open an issue in the [*Gerrie* tracker](https://github.com/andygrunwald/Gerrie/issues).