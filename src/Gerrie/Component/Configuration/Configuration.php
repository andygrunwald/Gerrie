<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Component\Configuration;

use Symfony\Component\Yaml\Yaml;

class Configuration
{

    protected $config = array();

    public function __construct($configFile)
    {
        $this->config = Yaml::parse($configFile);
    }

    /**
     * Returns the whole configuration
     *
     * @return array
     */
    public function getConfiguration()
    {
        return $this->config;
    }

    public function getConfigurationValue($valuePath)
    {
        $value = $this->getConfiguration();

        $pathParts = explode('.', $valuePath);
        foreach ($pathParts as $pathPart) {
            $value = $value[$pathPart];
        }

        return $value;
    }
}