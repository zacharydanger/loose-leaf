<?php
require_once 'global.php';

class ImageTest extends PHPUnit_Framework_TestCase {
	protected $testdata_dir;
	protected $goodImage;
	
	function setUp() {
		$this->testdata_dir = dirname(__FILE__);
		$this->goodImage = new Image($this->testdata_dir . '/testdata/stencil-clem.gif');
	}
	
	/**
	 * @expectedException Exception
	 */
	function testConstructorBadFile() {
		$I = new Image();
		$I = new Image(sha1(time()));
	}
	
	/**
	 * @dataProvider percentageProvider
	 */
	function testResizePercentage($percentage) {
		$I = $this->goodImage;		
		$I->resizePercentage($percentage);
	}
	
	/**
	 * @dataProvider pixelProvider
	 */
	function testResizeToX($X) {
		$I = $this->goodImage;
		$I->resizeToX($X);
		$image = $I->getImage();
		$this->assertEquals($X, imagesx($image));
	}
	
	/**
	 * @dataProvider pixelProvider
	 */
	function testResizeToY($Y) {
		$I = $this->goodImage;
		$I->resizeToY($Y);
		$image = $I->getImage();
		$this->assertEquals($Y, imagesy($image));
	}
	
	function pixelProvider() {
		$pixel = array();
		$pixel[] = array(10);
		$pixel[] = array(50);
		$pixel[] = array(51);
		return $pixel;
	}
	
	function testSave() {
		$I = $this->goodImage;
		$file_name = '/tmp/' . sha1(time() . rand(0,9)) . '.jpg';
		$I->save($file_name);
		$this->assertTrue(file_exists($file_name));
	}
	
	/**
	 * @dataProvider pixelProvider
	 */
	function testResizeTo($size) {
		$I = $this->goodImage;
		$I->resizeTo($size, $size);
		$image = $I->getImage();
		$this->assertEquals($size, imagesx($image));
		$this->assertEquals($size, imagesy($image));
	}
	
	function testResizeToFillToX() {
		$I = $this->goodImage;
		$new_y = ceil(imagesy($I->getImage()));
		$new_x = ceil(imagesx($I->getImage()) *.75);
		$I->resizeTo($new_x, $new_y);
		$image = $I->getImage();
		$this->assertEquals($new_x, imagesx($image), "X doesn't match new X $new_x");
		$this->assertEquals($new_y, imagesy($image), "Y doesn't match new Y $new_y");
	}
	
	function percentageProvider() {
		$percentages = array();
		$percentages[] = array(25);
		//$percentages[] = array(50);
		//$percentages[] = array(75);		
		//$percentages[] = array(110																																																																																																																																																																																																																																																																																																																																			);
		//$percentages[] = array(150);
		//$percentages[] = array(200);
		
		return $percentages;
	}
	
	function testResizeToFillerColor() {
		$this->markTestIncomplete();
		/*
		$I = $this->goodImage;
		$new_y = imagesy($I->getImage());
		$new_x = imagesx($I->getImage());
		
		$original_rgb = imagecolorat($I->getImage(), 0, 0);
		
		echo "\n---$original_rgb---\n";
		
		//$original_alpha = ($original_rgb >> 24) & 0xFF;
		$original_r = ($original_rgb >> 16) & 0xFF;
		$original_g = ($original_rgb >> 8) & 0xFF;
		$original_b = $original_rgb & 0xFF;
		
		echo "\n###\n";
		var_dump($original_rgb, $original_r, $original_g, $original_b);
		echo "\n###\n";
		
		$I->resizeTo($new_x, $new_y, "FF", "FF", "FF");
		$rgb = imagecolorat($I->getImage(), 1, 1);
		
		$new_r = ($rgb >> 16) & 0xFF;
		$new_g = ($rgb >> 8) & 0xFF;
		$new_b = $rgb & 0xFF;
		
		echo "\n---$rgb---\n";
		
		var_dump($rgb, $new_r, $new_b, $new_g);
		
		$this->assertNotEquals($original_r, $new_r, "bad R value");
		$this->assertNotEquals($original_b, $new_b, "bad B value");
		$this->assertNotEquals($original_g, $new_g, "bad G value");
		*/
	}
}
?>