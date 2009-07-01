<?php
/**
 * Class for resizing/manipulating image files based on the gd lib.
 */
class Image {
	private $_original_file = null;
	private $_original_image = null;
	private $_image;

	public function __construct($file_name = null) {
		$this->_original_file = $file_name;
		if(true == file_exists($file_name)) {
			$this->_original_image = imagecreatefromstring(file_get_contents($this->_original_file));
			$this->_image = $this->_original_image;
		} else {
			throw new Exception("Image file '" . $file_name . "' does not exist.");
		}
	}

	/**
	 * Resizes to a given percentage while maintaining the aspect ratio.
	 */
	public function resizePercentage($percentage = 1) {
		$original_x = imagesx($this->_original_image);
		$original_y = imagesy($this->_original_image);

		$new_x = $original_x * $percentage;
		$new_y = $original_y * $percentage;

		$this->_image = imagecreatetruecolor($new_x, $new_y);
		imagecopyresized($this->_image, $this->_original_image, 0, 0, 0, 0, $new_x, $new_y, $original_x, $original_y);
	}

	/**
	 * Resizes an image to a width constraint while maintaining the aspect ratio.
	 */
	public function resizeToX($new_x) {
		$original_x = imagesx($this->_original_image);
		$percentage = $new_x / $original_x;
		$this->resizePercentage($percentage);
	}

	/**
	 * Makes an image fit into a height constraint while keeping the aspect ratio.
	 */
	public function resizeToY($new_y) {
		$original_y = imagesy($this->_original_image);
		$percentage = $new_y / $original_y;
		$this->resizePercentage($percentage);
	}

	/**
	 * Returns the "altered" image.
	 */
	public function getImage() {
		return $this->_image;
	}

	/**
	 * Saves the image to a jpeg file.
	 */
	public function save($new_file, $quality = 100) {
		$quality = abs(intval($quality));
		$quality = ($quality > 100) ? 100 : $quality;
		imagejpeg($this->_image, $new_file, $quality);
	}

	/**
	 * Resizes an image to a set width / height. If the image doesn't match the given constraints
	 * it will maintain the images original aspect ratio and center a resized image on a filled
	 * background.
	 *
	 * The filled background can be specified via the $fill_red/green/blue parameters which take hex values.
	 */
	public function resizeTo($x, $y, $fill_red = "FF", $fill_green = "FF", $fill_blue = "FF") {
		$this->resizeToY($y);
		if(imagesx($this->_image) > $x) {
			$this->resizeToX($x);
		}
		$original_x = imagesx($this->_image);
		$original_y = imagesy($this->_image);
		$dest_x = ($original_x < $x) ? ceil(($x - $original_x) / 2) : 0;
		$dest_y = ($original_y < $y) ? ceil(($y - $original_y) / 2) : 0;

		$image = imagecreatetruecolor($x, $y);
		$fill_color = imagecolorallocate($image, hexdec($fill_red), hexdec($fill_green), hexdec($fill_blue));
		imagefill($image, 0, 0, $fill_color);
		imagecopymerge($image, $this->_image, $dest_x, $dest_y, 0, 0, $original_x, $original_y, 100);

		$this->_image = $image;
	}
}
?>