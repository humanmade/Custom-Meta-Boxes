<?php
/**
 * Small text field.
 *
 * @since 1.0.0
 *
 * @extends CMB_Text_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Text_Small_Field extends CMB_Text_Field {

	/**
	 * Print out field HTML.
	 */
	public function html() {

		$this->args['class'] .= ' cmb_text_small';

		parent::html();

	}
}
