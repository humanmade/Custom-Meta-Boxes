<?php
/**
 * Standard title used as a splitter.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Title extends CMB_Field {

	/**
	 * Print out field HTML - in this case we only want a title.
	 */
	public function title() {
		?>

		<div class="field-title">
			<h2 <?php $this->class_attr(); ?>>
				<?php echo esc_html( $this->title ); ?>
			</h2>
		</div>

		<?php

	}

	/**
	 * Placeholder for abstracted method.
	 */
	public function html() {}
}
