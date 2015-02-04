<?php

namespace Fusio\Backend\Api\Controller;

use Fusio\Backend\Filter\Controller as Filter;
use PSX\Filter as PSXFilter;
use PSX\Validate;
use PSX\Validate\Property;
use PSX\Validate\RecordValidator;

trait ValidatorTrait
{
	protected function getValidator()
	{
		return new RecordValidator(new Validate(), array(
			new Property('id', Validate::TYPE_INTEGER, array(new PSXFilter\PrimaryKey($this->tableManager->getTable('Fusio\Backend\Table\Controller')))),
			new Property('name', Validate::TYPE_STRING),
			new Property('class', Validate::TYPE_STRING),
			new Property('method', Validate::TYPE_STRING),
			new Property('config', Validate::TYPE_STRING),
		));
	}
}