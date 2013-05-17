<?php

namespace Gerrie\Helper;

use Symfony\Component\Yaml\Yaml;

class Configuration {

	protected $config = array();

	public function __construct($configFile = '') {

		if (!$configFile) {
			$configFile = CONFIG_FILE;
		}

		$this->config = Yaml::parse($configFile);
	}

	/**
	 * Returns the whole configuration
	 *
	 * @return array
	 */
	public function getConfiguration() {
		return $this->config;
	}

	public function getConfigurationValue($valuePath) {
		$value = $this->getConfiguration();

		$pathParts = explode('.', $valuePath);
		foreach ($pathParts as $pathPart) {
			$value = $value[$pathPart];
		}

		return $value;
	}
}