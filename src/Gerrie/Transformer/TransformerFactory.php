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

class TransformerFactory
{

    /**
     * Creates a new (data) transformer
     *
     * @param string $entity Entity / name of data to transform (singular)
     * @param bool $debug
     * @return TransformerInterface
     */
    public function getTransformer($entity, $debug = false)
    {
        $className = 'Gerrie\\Transformer\\' . $entity . 'Transformer';
        $transformer = new $className();

        if ($debug === true) {
            $transformer = new DebugTransformDecorator($transformer);
        }

        return $transformer;
    }
}