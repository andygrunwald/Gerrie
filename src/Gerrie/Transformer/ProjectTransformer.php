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

class ProjectTransformer extends BaseTransformer
{

    /**
     * Transforms the data into one unique format
     *
     * @return array
     */
    public function transform()
    {
        $data = $this->getData();

        $transformedData = array(
            'identifier' => ((isset($data['id']) === true) ? $data['id'] : ''),
            'name' => $data['_name'],
            'description' => ((isset($data['description']) === true) ? $data['description'] : ''),
            'kind' => ((isset($data['kind']) === true) ? $data['kind'] : ''),
            'state' => ((isset($data['state']) === true) ? $data['state'] : '')
        );

        return $transformedData;
    }
}