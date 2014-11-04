<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Tests\Component\Console;

use Gerrie\Component\Console\ArgvInputExtended;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

class ArgvInputExtendedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideOptions
     */
    public function testParseOptions($input, $options, $requestedOption, $expectedResult)
    {
        $input = new ArgvInputExtended($input);
        $input->bind(new InputDefinition($options));

        $this->assertEquals($expectedResult, $input->isOptionSet($requestedOption));
    }

    public function provideOptions()
    {
        return array(
            array(
                array('cli.php'),
                array(new InputOption('foo')),
                'foo',
                false,
            ),
            array(
                array('cli.php', '--foo'),
                array(new InputOption('foo')),
                'foo',
                true,
            ),
            array(
                array('cli.php', '--foo=bar'),
                array(new InputOption('foo', 'f', InputOption::VALUE_REQUIRED)),
                'foo',
                true
            ),
            array(
                array('cli.php', '--foo', '--bar'),
                array(
                    new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL),
                    new InputOption('bar', 'b', InputOption::VALUE_OPTIONAL)
                ),
                'bar',
                true,
            ),
            array(
                array('cli.php', '--foo', '--bar'),
                array(
                    new InputOption('foo', 'f', InputOption::VALUE_OPTIONAL),
                    new InputOption('bar', 'b', InputOption::VALUE_OPTIONAL)
                ),
                'baz',
                false,
            ),
            array(
                array('cli.php', '-f'),
                array(new InputOption('foo', 'f')),
                'foo',
                true,
            ),
            array(
                array('cli.php', '-fbar'),
                array(new InputOption('foo', 'f', InputOption::VALUE_REQUIRED)),
                'foo',
                true,
            ),
        );
    }
}
