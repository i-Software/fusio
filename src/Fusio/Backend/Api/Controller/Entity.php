<?php

namespace Fusio\Backend\Api\Controller;

use PSX\Api\Documentation;
use PSX\Api\Version;
use PSX\Api\View;
use PSX\Controller\SchemaApiAbstract;
use PSX\Data\RecordInterface;
use PSX\Http\Exception as StatusCode;
use PSX\Sql\Condition;

/**
 * Controller
 *
 * @see http://phpsx.org/doc/design/controller.html
 */
class Entity extends SchemaApiAbstract
{
	use ValidatorTrait;

	/**
	 * @Inject
	 * @var PSX\Data\Schema\SchemaManagerInterface
	 */
	protected $schemaManager;

	/**
	 * @Inject
	 * @var PSX\Sql\TableManager
	 */
	protected $tableManager;

	/**
	 * @return PSX\Api\DocumentationInterface
	 */
	public function getDocumentation()
	{
		$message = $this->schemaManager->getSchema('Fusio\Backend\Schema\Message');
		$view = new View();
		$view->setGet($this->schemaManager->getSchema('Fusio\Backend\Schema\Controller'));
		$view->setPut($this->schemaManager->getSchema('Fusio\Backend\Schema\Controller\Update'), $message);
		$view->setDelete(null, $message);

		return new Documentation\Simple($view);
	}

	/**
	 * Returns the GET response
	 *
	 * @param PSX\Api\Version $version
	 * @return array|PSX\Data\RecordInterface
	 */
	protected function doGet(Version $version)
	{
		$routeId = (int) $this->getUriFragment('route_id');
		$route   = $this->tableManager->getTable('Fusio\Backend\Table\Controller')->get($routeId);

		if(!empty($route))
		{
			return $route;
		}
		else
		{
			throw new StatusCode\NotFoundException('Could not find route');
		}
	}

	/**
	 * Returns the POST response
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @param PSX\Api\Version $version
	 * @return array|PSX\Data\RecordInterface
	 */
	protected function doCreate(RecordInterface $record, Version $version)
	{
	}

	/**
	 * Returns the PUT response
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @param PSX\Api\Version $version
	 * @return array|PSX\Data\RecordInterface
	 */
	protected function doUpdate(RecordInterface $record, Version $version)
	{
		$routeId = (int) $this->getUriFragment('route_id');
		$route   = $this->tableManager->getTable('Fusio\Backend\Table\Controller')->get($routeId);

		if(!empty($route))
		{
			$this->getValidator()->validate($record);

			$this->tableManager->getTable('Fusio\Backend\Table\Controller')->update(array(
				'id'         => $route['id'],
				'methods'    => $record->getMethods(),
				'path'       => $record->getPath(),
				'controller' => $record->getController(),
			));

			return array(
				'success' => true,
				'message' => 'Controller successful updated',
			);
		}
		else
		{
			throw new StatusCode\NotFoundException('Could not find route');
		}
	}

	/**
	 * Returns the DELETE response
	 *
	 * @param PSX\Data\RecordInterface $record
	 * @param PSX\Api\Version $version
	 * @return array|PSX\Data\RecordInterface
	 */
	protected function doDelete(RecordInterface $record, Version $version)
	{
		$routeId = (int) $this->getUriFragment('route_id');
		$route   = $this->tableManager->getTable('Fusio\Backend\Table\Controller')->get($routeId);

		if(!empty($route))
		{
			$this->tableManager->getTable('Fusio\Backend\Table\Controller')->delete(array(
				'id' => $route['id']
			));

			return array(
				'success' => true,
				'message' => 'Controller successful deleted',
			);
		}
		else
		{
			throw new StatusCode\NotFoundException('Could not find route');
		}
	}
}