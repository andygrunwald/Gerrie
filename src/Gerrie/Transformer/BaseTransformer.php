<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\Transformer;

abstract class BaseTransformer implements TransformerInterface
{

    /**
     * Data to transform
     *
     * @var array
     */
    private $data;

    /**
     * Set the data to transform
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * Returns the data to transform
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Returns the supported index names by the Gerrit API.
     *
     * @return array
     */
    public function getSupportedKeys()
    {
        return $this->supportedKeys;
    }

    /**
     * Checks if the current data transformer is responsible to transform the given data.
     *
     * @return boolean
     */
    public function isResponsible()
    {
        return false;
    }

    /**
     * Transforms the data into one unique format
     *
     * @return array
     */
    abstract public function transform();
}