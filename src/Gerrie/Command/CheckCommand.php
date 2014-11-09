<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Command;

use Gerrie\Check\ConfigFileValidationCheck;
use Gerrie\Check\CurlExtensionCheck;
use Gerrie\Check\ConfigFileCheck;
use Gerrie\Check\PDOMySqlExtensionCheck;
use Gerrie\Check\SSHCheck;
use Gerrie\Check\CheckInterface;
use Gerrie\Check\DatabaseConnectionCheck;
use Gerrie\Component\Configuration\Configuration;
use Gerrie\Component\Configuration\ConfigurationFactory;
use Gerrie\Component\Database\Database;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckCommand extends GerrieBaseCommand
{
    /**
     * OK sign from unicode table
     *
     * @link http://apps.timwhitlock.info/emoji/tables/unicode
     * @var string
     */
    const EMOJI_OK = "\xE2\x9C\x85";

    /**
     * Failure sign from unicode table
     *
     * @link http://apps.timwhitlock.info/emoji/tables/unicode
     * @var string
     */
    const EMOJI_FAILURE = "\xE2\x9D\x8C";

    /**
     * Question mark sign from unicode table
     *
     * @link http://apps.timwhitlock.info/emoji/tables/unicode
     * @var string
     */
    const EMOJI_OPTIONAL = "\xE2\x9D\x93";

    /**
     * Stores the overall result of all checks.
     *
     * @var bool
     */
    private $overallResult = true;

    protected function configure()
    {
        $this
            ->setName('gerrie:check')
            ->setDescription('Checks if the setup is working');
        $this->addConfigFileOption();
        $this->addDatabaseOptions();
        $this->addSSHKeyOption();
        $this->addInstancesArgument();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var InputExtendedInterface $input */

        /**
         * Welcome message
         */
        // Run gerrie:create-database-Command
        $output->writeln('<info>Gerrie will check if the current setup will work as expected.</info>');
        $output->writeln('<info>Lets start!</info>');

        /**
         * Start checking
         */
        $output->writeln('');
        $output->writeln('<comment>System:</comment>');

        // Check if curl is installed
        $curlCheck = new CurlExtensionCheck();
        $this->checkProperty($output, $curlCheck);

        // Check if PDO and MySQL are installed
        $pdoCheck = new PDOMySqlExtensionCheck();
        $this->checkProperty($output, $pdoCheck);

        // Check if SSH is installed
        $sshCheck = new SSHCheck();
        $this->checkProperty($output, $sshCheck);

        $output->writeln('');
        $output->writeln('<comment>Configuration:</comment>');

        // Check if the config file exists
        $configFileCheck = new ConfigFileCheck($input->getOption('config-file'));
        $this->checkProperty($output, $configFileCheck);

        // Check if the config file is valid
        $configFile = $input->getOption('config-file');
        try {
            $configuration = ConfigurationFactory::getConfigurationByConfigFileAndCommandOptionsAndArguments($configFile, $input);
        } catch (\Exception $e) {
            $configuration = new Configuration();
        }

        $configFileValidationCheck = new ConfigFileValidationCheck($configuration);
        $this->checkProperty($output, $configFileValidationCheck);

        $output->writeln('');
        $output->writeln('<comment>Connection:</comment>');

        // Check database connection
        $databaseConfig = $configuration->getConfigurationValue('Database');
        $database = new Database($databaseConfig, false);

        $databaseConnectionCheck = new DatabaseConnectionCheck($database);
        $this->checkProperty($output, $databaseConnectionCheck);

        // SSH Connection works (only with host, request Gerrie version)
        // Curl Connection works (only with host, request Gerrie version)


        /**
         * Message end result
         */
        if ($this->overallResult === false) {
            $this->outputFixMessage($output);
        } else {
            $this->outputEverythingIsFineAndIWantABeerMessage($output);
        }
    }

    /**
     * Checks a single property
     *
     * @param OutputInterface $output
     * @param CheckInterface $property
     * @return bool
     */
    protected function checkProperty(OutputInterface $output, CheckInterface $property)
    {
        $result = $property->check();

        if($result === false) {
            $outputLevel = 'error';
            $sign = self::EMOJI_FAILURE;

            if ($property->isOptional() === true) {
                $outputLevel = 'comment';
                $sign = self::EMOJI_OPTIONAL;
            }

            $message = $property->getFailureMessage();

            // The default value from overallResult is true.
            // So it is enough to set it to false once.
            $this->overallResult = false;

        } elseif ($result === true) {
            $outputLevel = 'info';
            $sign = self::EMOJI_OK;
            $message = $property->getSuccessMessage();
        }

        $outputMessage = "  <info>%s</info>   <%s>%s</%s>";
        $outputMessage = sprintf($outputMessage, $sign, $outputLevel, $message, $outputLevel);
        $output->writeln($outputMessage);

        return $result;
    }

    /**
     * If minimum one check fails (optional or not does not matter), this message will be outputted.
     *
     * @param OutputInterface $output
     * @return void
     */
    protected function outputFixMessage(OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln("<error>    Oh dear \xF0\x9F\x98\xA2     </error>");
        $output->writeln('');
        $output->writeln("<info>Sadly, not all checks went well</info>");
        $output->writeln('<info>But this is no reason to give up!</info>');
        $output->writeln('<info>Remember: This check was to verifiy if Gerrie is working to avoid unnecessary problems.</info>');
        $output->writeln("<info>So get up, grab a \xE2\x98\x95  and try to fix the failing checks.</info>");
        $output->writeln('');
        $output->writeln('<info>If you don`t know how to solve this "problem(s)":</info>');
        $output->writeln('<info>  * have a look at the documentation</info>');
        $output->writeln('<info>  * or open an issue on GitHub</info>');
        $output->writeln('');
        $output->writeln('<info>Do not be afraid. You can execute this check whenever you want.</info>');
        $output->writeln('<info>It is for free and breaks nothing :)</info>');
        $output->writeln("<info>So keep \xF0\x9F\x8E\xB8  and cu next time \xF0\x9F\x91\x8B</info>");
        $output->writeln('');
    }

    /**
     * If everything is fine and all checks went good, this message will be outputted.
     *
     * @param OutputInterface $output
     * @return void
     */
    protected function outputEverythingIsFineAndIWantABeerMessage(OutputInterface $output)
    {
        $output->writeln('');
        $output->writeln("<info>Wow! Everything works fine. Gratulations \xF0\x9F\x8E\x81</info>");
        $output->writeln('');
        $output->writeln('<info>Now you are ready to use Gerrie with the full featureset. Awesome!</info>');

        $message  = "<info>Grab a \xF0\x9F\x8D\xBA  (better \xF0\x9F\x8D\xBB ) or a \xF0\x9F\x8D\xB7  ";
        $message .= "and start crawling your Gerrit instances.</info>";
        $output->writeln($message);
        $output->writeln("<info>Have fun \xF0\x9F\x98\x83</info>");
        $output->writeln('');
    }
}