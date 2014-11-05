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

## How to start

TODO

## Documentation

The complete and detailed documentation can be found at [*Gerrie* @ Read the Docs](https://gerrie.readthedocs.org/en/latest/).
The documentation is written in [reStructuredText](http://en.wikipedia.org/wiki/ReStructuredText) and shipped with the source code and can be found in the [*docs/*](https://github.com/andygrunwald/Gerrie/tree/master/docs) folder.

## Source code

The source code can be found at [*andygrunwald/Gerrie @ GitHub*](https://github.com/andygrunwald/Gerrie).

## Contributing

TODO move this to documentation

* Fork and clone it (`git clone https://github.com/andygrunwald/Gerrie.git`)
* Create your feature branch (`git checkout -b my-new-feature`)
* Make your changes (hack hack hack)
* Commit your changes (`git commit -am 'Add some feature'`)
* Push to the branch (`git push origin my-new-feature`)
* Create new Pull Request

A more detailed guide can be found at githubs [Fork A Repo](https://help.github.com/articles/fork-a-repo).

### Coding Styleguide

TODO move this to documentation

For convenience we follow the [PSR-1](http://www.php-fig.org/psr/psr-1/) and [PSR-2](http://www.php-fig.org/psr/psr-2/) coding styleguides of [PHP Framework Interop Group](http://www.php-fig.org/).

Please be so nice to take care of this during code contribution (e.g. pull requests).
To check your code against this standards you can use the tool [PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer/) for this.

## License

This project is released under the terms of the [MIT license](http://en.wikipedia.org/wiki/MIT_License).

## Support, contact or feedback

If you got questions, got feedback, getting crazy with setting up or using this project or want to drink a :beer: and talk about this project **just contact me**.

Write me an email (see [*Andy @ GitHub*](https://github.com/andygrunwald)) or tweet me ([@andygrunwald](http://twitter.com/andygrunwald)).
And of course, you can just open an issue in the [*Gerrie* tracker](https://github.com/andygrunwald/Gerrie/issues).