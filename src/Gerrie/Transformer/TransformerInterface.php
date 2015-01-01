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

interface TransformerInterface
{

    /**
     * Set the data to transform
     *
     * @param array $data
     * @return void
     */
    public function setData(array $data);

    /**
     * Returns the data to transform
     *
     * @return array
     */
    public function getData();

    /**
     * Checks if the current data transformer is responsible to transform the given data.
     *
     * @return boolean
     */
    public function isResponsible();

    /**
     * Transforms the data into one unique format
     *
     * @return array
     */
    public function transform();
}