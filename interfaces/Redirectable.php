<?php
/**
 * Interface to make an object redirectable.
 */
interface Redirectable {
	/**
	 * Returns the location this object wants to redirect to.
	 */
	public function getLocation();
}
?>
