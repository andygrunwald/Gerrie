<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Check;

use Gerrie\Component\DataService\DataServiceInterface;

/**
 * Checks if the API connection via SSH or REST works as expected.
 *
 * @author Andreas Grunwald <andygrunwald@gmail.com>
 */
class APIConnectionCheck implements CheckInterface
{

    /**
     * DataService object
     *
     * @var DataServiceInterface
     */
    protected $dataService;

    /**
     * Version of Gerrit instance
     *
     * @var string
     */
    protected $version;

    public function __construct(DataServiceInterface $dataService)
    {
        $this->dataService = $dataService;
    }

    /**
     * Executes the check itselfs
     *
     * @return boolean
     */
    public function check()
    {
        $result = false;

        $this->version = $this->dataService->getVersion();
        if ($this->version) {
            $result = true;
        }

        return $result;
    }

    /**
     * Returns the message, if the check succeeded
     *
     * @return string
     */
    public function getSuccessMessage()
    {
        $message = 'Connection to Gerrit "%s" (v%s) via %s-DataService was successful and works as expected.';
        $message = sprintf($message, $this->dataService->getHost(), $this->version, $this->dataService->getName());

        return $message;
    }

    /**
     * Returns the message, if the check fails
     *
     * @return string
     */
    public function getFailureMessage()
    {
        $message  = 'Connection to Gerrit "%s" via %s-DataService was not successful. ';
        $message .= 'Please check your credentials or setup';
        $message = sprintf($message, $this->dataService->getHost(), $this->dataService->getName());

        return $message;
    }

    /**
     * Returns if this check is optional or required.
     *
     * @return bool
     */
    public function isOptional()
    {
        return false;
    }
}
