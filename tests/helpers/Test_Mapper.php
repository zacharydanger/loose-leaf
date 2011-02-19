<?php
namespace LooseLeaf\Tests;

class Test_Mapper extends \LooseLeaf\Data_Mapper {
	protected $record_class = '\LooseLeaf\Test_Model';

	protected $class_map = array(
		'model_id' => 'model_id',
		'foo' => 'field_1',
		'bar' => 'field_2'
	);

	protected $table = 'foobaz';

	protected $primary_key = 'model_id';
}
