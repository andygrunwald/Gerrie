<?php
/**
 * This file is part of the Gerrie package.
 *
 * (c) Andreas Grunwald <andygrunwald@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Gerrie\API\Repository;

use Gerrie\API\DataService\DataServiceInterface;
use Gerrie\Transformer\TransformerFactory;

class ProjectRepository
{
    /**
     * API connection
     *
     * @var DataServiceInterface
     */
    protected $dataService;

    /**
     * @var TransformerFactory
     */
    protected $transformerFactory;

    /**
     * @param DataServiceInterface $dataService
     * @param TransformerFactory $transformerFactory
     */
    public function __construct(DataServiceInterface $dataService, TransformerFactory $transformerFactory) {
        $this->setDataService($dataService);
        $this->setTransformerFactory($transformerFactory);
    }

    /**
     * Returns the data service for API communication
     *
     * @return DataServiceInterface
     */
    public function getDataService() {
        return $this->dataService;
    }

    /**
     * Sets the data service for API communication
     *
     * @param DataServiceInterface $dataService
     * @return void
     */
    public function setDataService(DataServiceInterface $dataService) {
        $this->dataService = $dataService;
    }

    /**
     * Returns the transformer factory
     *
     * @return TransformerFactory
     */
    public function getTransformerFactory() {
        return $this->transformerFactory;
    }

    /**
     * Sets the transformer factory
     *
     * @param TransformerFactory $transformerFactory
     * @return void
     */
    public function setTransformerFactory(TransformerFactory $transformerFactory) {
        $this->transformerFactory = $transformerFactory;
    }

    /**
     * Returns all projects by Gerrit API.
     * The returned projects are already transformed to a unique format.
     *
     * @param bool $debuggingEnabled
     * @return array
     * @throws \RuntimeException
     */
    public function getProjects($debuggingEnabled = false) {
        $dataService = $this->getDataService();
        $projects = $dataService->getProjects();

        if (is_array($projects) === false) {
            $message = 'No projects found on "%s"!';
            $message = sprintf($message, $dataService->getHost());
            throw new \RuntimeException($message, 1363894633);
        }

        $transformerFactory = $this->getTransformerFactory();
        $projectTransformer = $transformerFactory->getTransformer('Project', $debuggingEnabled);

        $transformedProjects = [];
        foreach ($projects as $name => $project) {
            if (array_key_exists('_name', $project)) {
                /**
                 * In "$project" we do not get the name of the project.
                 * The array index is the key.
                 * We want to transform this name as well, so we add this information with a kind of
                 * "reserved" keyword (prefixed with "_").
                 * But if this key is already taken (maybe in feature versions) this exception
                 * will be thrown and we have to take care about it and apply a change.
                 * Maybe get rid of $name, because this information is already in $project?
                 * Depends on the context.
                 *
                 * If you saw this exception during using Gerrie, please
                 * * fix the bug described above yourself and make a pull request
                 * * or open an issue on the github project that we can take care of this.
                 *
                 * Thanks.
                 */
                $exceptionMessage  = 'Key "_name" already exists. Maybe we can get rid of the $name var?';
                $exceptionMessage .= 'Please search for this exception and read the comments.';
                throw new \RuntimeException($exceptionMessage, 1420132548);
            }
            $project['_name'] = $name;

            // Transform data
            $projectTransformer->setData($project);
            $transformedProjects[$name] = $projectTransformer->transform();
        }

        return $transformedProjects;
    }
}