<?php
/**
 * Code style text field.
 *
 * Args:
 *  - int "rows" - number of rows in the <textarea>
 *
 * @since 1.0.0
 *
 * @extends CMB_Textarea_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Textarea_Field_Code extends CMB_Textarea_Field {

	/**
	 * Print out field HTML.
	 */
	public function html() {

		$this->args['class'] .= ' code';

		parent::html();

	}
}
