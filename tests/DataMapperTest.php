<?php
namespace LooseLeaf\Tests;

require_once __DIR__ . '/../Data_Mapper.php';
require_once __DIR__ . '/helpers/TestCase.php';
require_once __DIR__ . '/helpers/Test_Model.php';
require_once __DIR__ . '/helpers/Test_Mapper.php';

use \LooseLeaf\Test_Model;

class DataMapperTest extends TestCase {
	public function testGet_PreparesQuery() {

		$mock_statement = $this->getMock('\Std_Class', array('bindValue', 'execute', 'fetchAll'));

		$mock_statement->expects($this->once())
			->method('bindValue')
			->with('key', 1337);

		$mock_statement->expects($this->once())
			->method('execute')
			->will($this->returnValue(true));

		$mock_statement->expects($this->once())
			->method('fetchAll')
			->will($this->returnValue(array(
				array(
					'model_id' => 1337,
					'foo' => 'fooooo',
					'bar' => 'barr'
				)
			)));

		$pdo = $this->getMockPDO();
		$pdo->expects($this->once())
			->method('prepare')
			->with('select `model_id`, `foo`, `bar` from `foobaz` where `model_id` = :key')
			->will($this->returnValue($mock_statement));

		$TM = new Test_Mapper($pdo);

		$expected_model = new Test_Model();
		$expected_model->ID = 1337;
		$expected_model->model_id = 1337;
		$expected_model->field_1 = 'fooooo';
		$expected_model->field_2 = 'barr';

		$this->assertEquals($expected_model, $TM->get(1337));
	}

	/**
	 * @expectedException \LooseLeaf\Data_Mapper_Exception
	 */
	public function testSave_InsertBadInsert() {
		$mock_statement = $this->getMockPDOStatement();
		$mock_statement->expects($this->once())
			->method('execute')
			->will($this->returnValue(false));

		$model = new Test_Model();

		$pdo = $this->getMockPDO();

		$pdo->expects($this->once())
			->method('prepare')
			->will($this->returnValue($mock_statement));
		$pdo->expects($this->never())
			->method('lastInsertId');

		$TM = new Test_Mapper($pdo);
		$TM->save($model);
	}

	public function testSave_Insert() {
		$mock_statement = $this->getMockPDOStatement();
		$mock_statement->expects($this->once())
			->method('execute')
			->with(array(
				':foo' => 'foo_value',
				':bar' => 'bar_value'
			))
			->will($this->returnValue(true));

		$model = new Test_Model();

		$model->field_1 = 'foo_value';
		$model->field_2 = 'bar_value';

		$pdo = $this->getMockPDO();

		$pdo->expects($this->once())
			->method('prepare')
			->with("INSERT INTO `foobaz` (`foo`, `bar`) VALUES (:foo, :bar)")
			->will($this->returnValue($mock_statement));
		$pdo->expects($this->once())
			->method('lastInsertId')
			->will($this->returnValue(1337));

		$TM = new Test_Mapper($pdo);

		$TM->save($model);

		$this->assertEquals(1337, $model->ID, "Model ID not updated.");
	}

	/**
	 * @expectedException \LooseLeaf\Data_Mapper_Exception
	 */
	public function testSave_Update_BadUpdate() {
		$mock_statement = $this->getMockPDOStatement();
		$mock_statement->expects($this->once())
			->method('execute')
			->will($this->returnValue(false));

		$model = new Test_Model();
		$model->ID = 1337;
		$model->model_id = 1337;
		$model->field_1 = 'foo_value';
		$model->field_2 = 'bar_value';

		$pdo = $this->getMockPDO();

		$pdo->expects($this->once())
			->method('prepare')
			->will($this->returnValue($mock_statement));

		$TM = new Test_Mapper($pdo);
		$TM->save($model);
	}

	public function testSave_Update() {
		$mock_statement = $this->getMockPDOStatement();
		$mock_statement->expects($this->once())
			->method('execute')
			->with(array(
				':key' => 1337,
				':foo' => 'foo_value',
				':bar' => 'bar_value'
			))
			->will($this->returnValue(true));

		$model = new Test_Model();
		$model->ID = 1337;
		$model->model_id = 1337;
		$model->field_1 = 'foo_value';
		$model->field_2 = 'bar_value';

		$pdo = $this->getMockPDO();

		$pdo->expects($this->once())
			->method('prepare')
			->with("UPDATE `foobaz` SET `foo` = :foo, `bar` = :bar WHERE `model_id` = :key")
			->will($this->returnValue($mock_statement));

		$TM = new Test_Mapper($pdo);

		$TM->save($model);

		$this->assertEquals(1337, $model->ID, "Model ID not updated.");
	}

	public function testDelete_NonSavedObject() {
		$pdo = $this->getMockPDO();
		$pdo->expects($this->never())
			->method('prepare');

		$TM = new Test_Mapper($pdo);
		$this->assertNull($TM->delete(new Test_Model()));
	}

	/**
	 * @expectedException \LooseLeaf\Data_Mapper_Exception
	 */
	public function testDelete_BadQuery() {
		$mock_statement = $this->getMockPDOStatement();
		$mock_statement->expects($this->once())
			->method('execute')
			->will($this->returnValue(false));

		$pdo = $this->getMockPDO();
		$pdo->expects($this->once())
			->method('prepare')
			->will($this->returnValue($mock_statement));

		$model = new Test_Model();
		$model->ID = 1337;
		$model->model_id = 1337;

		$TM = new Test_Mapper($pdo);
		$TM->delete($model);
	}

	public function testDelete_GoodDelete() {
		$mock_statement = $this->getMockPDOStatement();
		$mock_statement->expects($this->once())
			->method('execute')
			->with(array(':key' => 1337))
			->will($this->returnValue(true));

		$pdo = $this->getMockPDO();
		$pdo->expects($this->once())
			->method('prepare')
			->with("DELETE FROM `foobaz` WHERE `model_id` = :key")
			->will($this->returnValue($mock_statement));

		$model = new Test_Model();
		$model->ID = 1337;
		$model->model_id = 1337;

		$TM = new Test_Mapper($pdo);
		$TM->delete($model);

		$this->assertEquals(0, $model->ID, "ID wasn't unset.");
		$this->assertNull($model->model_id, "model_id wasn't unset");
	}
}
