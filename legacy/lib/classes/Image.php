<?php
/**
 * Class for resizing/manipulating image files based imagick.
 */
class Image {
	private $_original_file = null;
	private $_original_image = null;
	private $_image;

	public function __construct($file_name = null) {
		$this->_original_file = $file_name;
		if(true == file_exists($file_name)) {
			$this->_original_image = new Imagick($this->_original_file);
			$this->_image = $this->_original_image;
		} else {
			throw new Exception("Image file '" . $file_name . "' does not exist.");
		}
	}

	/**
	 * Resizes to a given percentage while maintaining the aspect ratio.
	 */
	public function resizePercentage($percentage = 1) {
		$original_x = $this->_original_image->getImageWidth();
		$original_y = $this->_original_image->getImageHeight();

		$new_x = ceil($original_x * $percentage);
		$new_y = ceil($original_y * $percentage);

		$this->_image->resizeImage($new_x, $new_y, imagick::FILTER_UNDEFINED, 1, true);
	}

	/**
	 * Resizes an image to a width constraint while maintaining the aspect ratio.
	 */
	public function resizeToX($new_x) {
		$original_x = $this->_original_image->getImageWidth();
		$percentage = $new_x / $original_x;
		$this->resizePercentage($percentage);
	}

	/**
	 * Makes an image fit into a height constraint while keeping the aspect ratio.
	 */
	public function resizeToY($new_y) {
		$original_y = $this->_original_image->getImageHeight();
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
		$this->_image->writeImage($new_file);
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
		if($this->_image->getImageWidth() > $x) {
			$this->resizeToX($x);
		}
		$original_x = $this->_image->getImageWidth();
		$original_y = $this->_image->getImageHeight();
		$dest_x = ($original_x < $x) ? ceil(($x - $original_x) / 2) : 0;
		$dest_y = ($original_y < $y) ? ceil(($y - $original_y) / 2) : 0;


		$color = new ImagickPixel();
		$color->setColor("#" . $fill_red . $fill_green . $fill_blue);

		$this->_image->borderImage($color, $dest_x, $dest_y);
		$this->_image->cropImage($x, $y, 0, 0);
	}
}
?>