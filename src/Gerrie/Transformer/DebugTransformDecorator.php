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

class DebugTransformDecorator extends BaseTransformer
{

    /**
     * @var TransformerInterface
     */
    private $transformer;

    /**
     * Supported index names returned by the Gerrie API.
     *
     * @var array
     */
    protected $supportedKeys = [];

    /**
     * @param TransformerInterface $transformer
     */
    public function __construct(TransformerInterface $transformer)
    {
        $this->transformer = $transformer;
    }

    /**
     * Set the data to transform
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data)
    {
        $this->transformer->setData($data);
    }

    /**
     * Returns the data to transform
     *
     * @return array
     */
    public function getData()
    {
        return $this->transformer->getData();
    }

    /**
     * Checks if the current data transformer is responsible to transform the given data.
     *
     * @return boolean
     */
    public function isResponsible()
    {
        return $this->transformer->isResponsible();
    }

    /**
     * Transforms the data into one unique format
     *
     * @return array
     */
    public function transform()
    {
        $originalData = $this->getData();
        $transformedData = $this->transformer->transform();

        $restData = $this->unsetKeys($originalData, $this->transformer->getSupportedKeys());
        $this->checkIfAllValuesWereProceeded($restData, $this->transformer);

        return $transformedData;
    }

    /**
     * Unsets an amount of keys in given $data
     *
     * @param array $data Data array where the keys will be unset
     * @param array $keyList List of keys which will be unset
     * @return array
     */
    private function unsetKeys(array $data, array $keyList)
    {
        foreach ($keyList as $key) {
            if (isset($data[$key]) === true) {
                unset($data[$key]);
            }
        }

        return $data;
    }

    /**
     * Method to check if all data were imported.
     * Normally, this export script unsets the exported value after proceeding.
     *
     * If there are values left in the array, there could be
     * a) a bug, because the value is not unsetted
     * b) a change in the Gerrit server API
     * c) a bug, because not all values are exported / proceeded
     *
     * This methods help to detect this :)
     *
     * @param array $data Data to inspect
     * @param TransformerInterface $transformer The transformer class
     * @throws \Exception
     */
    private function checkIfAllValuesWereProceeded(array $data, TransformerInterface $transformer)
    {
        if (count($data) > 0) {
            var_dump($data);
            $message = 'Not all values were proceeded / exported. Please have a look at "%s"';
            $message = sprintf($message, get_class($transformer));
            throw new \RuntimeException($message, 1363894644);
        }
    }
}