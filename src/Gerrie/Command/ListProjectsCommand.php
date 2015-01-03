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

use Gerrie\Component\Configuration\ConfigurationFactory;
use Gerrie\API\DataService\DataServiceFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListProjectsCommand extends GerrieBaseCommand
{

    /**
     * Configuration object
     *
     * @var \Gerrie\Component\Configuration\Configuration
     */
    protected $configuration = null;

    protected function configure()
    {
        $this
            ->setName('gerrie:list-projects')
            ->setDescription('List/Displays all projects of a Gerrit instance');

        $this->addConfigFileOption();
        $this->addSSHKeyOption();
        $this->addInstancesArgument();
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        /** @var InputExtendedInterface $input */

        $configFile = $input->getOption('config-file');
        $this->configuration = ConfigurationFactory::getConfigurationByConfigFileAndCommandOptionsAndArguments($configFile, $input);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var InputExtendedInterface $input */

        // Start the importer for each configured project
        $gerritSystems = $this->configuration->getConfigurationValue('Gerrit');
        $defaultSSHKeyFile = $this->configuration->getConfigurationValue('SSH.KeyFile');

        $i = 0;
        foreach ($gerritSystems as $name => $gerrieProject) {
            $gerritSystem['Name'] = $name;

            foreach ($gerrieProject as $gerritInstance) {
                $path = parse_url($gerritInstance);

                // TODO Extract this Instance Key part here. This is the same as in "CrawlCommand".
                // Get instance url
                // If the instance is a string, we only got a url path like scheme://user@url:port/
                if (is_string($gerritInstance)) {
                    $instanceConfig = [
                        'Instance' => $gerritInstance,
                        'KeyFile' => $defaultSSHKeyFile
                    ];

                // If the instance is an array, we get a key => value structure with an Instance key
                } elseif (is_array($gerritInstance) && isset($gerritInstance['Instance'])) {
                    $instanceConfig = [
                        'Instance' => $gerritInstance['Instance'],
                        'KeyFile' => $defaultSSHKeyFile
                    ];

                    if (array_key_exists('KeyFile', $gerritInstance) === true) {
                        $instanceConfig['KeyFile'] = $gerritInstance['KeyFile'];
                    }
                } else {
                    throw new \RuntimeException('No Gerrit instance config given', 1415451921);
                }

                $dataService = DataServiceFactory::getDataService($instanceConfig);
                $projects = $dataService->getProjects();

                if (is_array($projects) === false) {
                    throw new \Exception('No projects found on "' . $path['host'] . '"!', 1363894633);
                }

                if ($i >= 1) {
                    $output->writeln('');
                }

                $headline = '<comment>Instance: %s (via %s)</comment>';
                $headline = sprintf($headline, $path['host'], $dataService->getName());
                $output->writeln($headline);
                $output->writeln('<comment>' . str_repeat('=', 40) . '</comment>');

                foreach ($projects as $name => $project) {
                    $message = '<info>%s</info>';
                    $message = sprintf($message, $name);
                    $output->writeln($message);
                }

                $i++;
            }
        }
    }
}