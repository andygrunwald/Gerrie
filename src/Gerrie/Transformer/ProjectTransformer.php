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
     * Supported index names returned by the Gerrie API.
     *
     * @var array
     */
    protected $supportedKeys = [
        'description',
        'parent',
        'id',
        'kind',
        'state'
    ];

    /**
     * Transforms the data into one unique format
     *
     * @return array
     */
    public function transform()
    {
        $data = $this->getData();

        // TODO Check "parent" key
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