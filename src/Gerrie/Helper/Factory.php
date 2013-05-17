<?php

namespace Gerrie\Helper;

class Factory {

	public static function getHTTPClientInstance(array $config) {
		$username = ((isset($config['HTTP']['Username']) === true) ? $config['HTTP']['Username']: '');
		$password = ((isset($config['HTTP']['Password']) === true) ? $config['HTTP']['Password']: '');

		// Bootstrap the rest client
		$curlClient = new \Buzz\Client\Curl();
		$curlClient->setVerifyPeer(FALSE);
		$restClient = new \Buzz\Browser($curlClient);

		if ($username && $password) {
			$authListener = new \Buzz\Listener\BasicAuthListener($username, $password);
			$restClient->addListener($authListener);
		}

		return $restClient;
	}
}