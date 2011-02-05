<?php
require_once __DIR__ . '/../Html_Template.php';
require_once __DIR__ . '/helpers/Html_Template_TestSub.php';

class HtmlTemplateTest extends PHPUnit_Extensions_OutputTestCase {
	/**
	 * @expectedException Exception
	 */
	public function testBadConstructor() {
		$HT = new LooseLeaf\Html_Template('foooooooooobar');
	}

	public function testGetVars_DefaultEmpty() {
		$HT = new Html_Template_TestSub();
		$vars = $HT->getVars();
		$this->assertEquals(0, count($vars));
	}

	public function testBind_OneVar() {
		$var_name = sha1(microtime(true));
		$var_val = md5(microtime(true));

		$HT = new Html_Template_TestSub();
		$HT->bind($var_name, $var_val);

		$vars = $HT->getVars();
		$this->assertEquals(1, count($vars));
		$this->assertEquals($var_val, $vars[$var_name]);
	}

	public function testGetFile() {
		$file = __DIR__ . '/helpers/test-template.php';
		$HT = new LooseLeaf\Html_Template($file);
		$this->assertEquals($file, $HT->getFile());
	}

	public function testRender_Output() {
		$BAR = sha1(microtime(true));
		$FOO = md5(microtime(true));
		$HT = new LooseLeaf\Html_Template(__DIR__ . '/helpers/test-template.php');
		$HT->bind('BAR', $BAR);
		$HT->bind('FOO', $FOO);

		$expected_output = $BAR . ' - ' . $FOO;
		$this->expectOutputString($expected_output);
		$HT->render();
	}

	public function testRender_Return() {
		$BAR = sha1(microtime(true));
		$FOO = md5(microtime(true));
		$HT = new LooseLeaf\Html_Template(__DIR__ . '/helpers/test-template.php');
		$HT->bind('BAR', $BAR);
		$HT->bind('FOO', $FOO);

		$expected_output = $BAR . ' - ' . $FOO;
		$this->expectOutputString(''); //output should be empty
		$actual_output = $HT->returnOutput();
		$this->assertEquals($expected_output, $actual_output);
	}

	public function testGetBindings_NoBindings() {
		$HT = $this->_getTestTemplate();
		$binding_list = $HT->getBindings();
		$this->assertInternalType('array', $binding_list);
		$this->assertEquals(0, count($binding_list));
	}

	public function testGetBindings_MultipleBindings() {
		$HT = $this->_getTestTemplate();
		$expected_bindings = array('foo' => 'bar', 'qwerty' => 'asdf');
		foreach($expected_bindings as $key => $val) {
			$HT->bind($key, $val);
		}
		$binding_list = $HT->getBindings();
		$this->assertEquals($expected_bindings, $binding_list);
	}

	public function testGetBinding_NotBound() {
		$HT = $this->_getTestTemplate();
		$this->assertNull($HT->getBinding('foobar'));
	}

	public function testGetBinding_Bound() {
		$key = sha1(microtime(true));
		$val = md5(microtime(true));
		$HT = $this->_getTestTemplate();
		$HT->bind($key, $val);
		$this->assertEquals($val, $HT->getBinding($key));
	}

	private function _getTestTemplate() {
		return new LooseLeaf\Html_Template(__DIR__ . '/helpers/test-template.php');
	}
}
?>
