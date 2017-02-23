<?php
/**
 * WYSIWYG field.
 *
 * @since 1.0.0
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_wysiwyg extends CMB_Field {

	/**
	 * Get default arguments for field including custom parameters.
	 *
	 * @return array Default arguments for field.
	 */
	public function default_args() {
		return array_merge(
			parent::default_args(),
			array(
				'options' => array(),
			)
		);
	}

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_script( 'cmb-wysiwyg', trailingslashit( CMB_URL ) . 'js/field-wysiwyg.js', array( 'jquery', 'cmb-scripts' ), CMB_VERSION );
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {

		$id   = $this->get_the_id_attr();
		$name = $this->get_the_name_attr();

		$field_id = $this->get_js_id();

		printf(
			'<div class="cmb-wysiwyg" data-id="%s" data-name="%s" data-field-id="%s">',
			esc_attr( $id ),
			esc_attr( $name ),
			esc_attr( $field_id )
		);

		if ( $this->is_placeholder() ) {

			// For placeholder, output the markup for the editor in a JS var.
			ob_start();
			$this->args['options']['textarea_name'] = 'cmb-placeholder-name-' . $field_id;
			wp_editor( '', 'cmb-placeholder-id-' . $field_id, $this->args['options'] );
			$editor = ob_get_clean();
			$editor = str_replace( array( "\n", "\r" ), '', $editor );
			$editor = str_replace( array( "'" ), '"', $editor );

			?>

			<script>
				if ( 'undefined' === typeof( cmb_wysiwyg_editors ) )
					var cmb_wysiwyg_editors = {};
				cmb_wysiwyg_editors.<?php echo esc_js( $field_id ); ?> = <?php echo wp_json_encode( $editor ); ?>;
			</script>

			<?php

		} else {

			$this->args['options']['textarea_name'] = $name;
			wp_editor( $this->get_value(), $id, $this->args['options'] );

		}

		echo '</div>';

	}

	/**
	 * Check if this is a placeholder field.
	 * Either the field itself, or because it is part of a repeatable group.
	 *
	 * @return bool
	 */
	public function is_placeholder() {

		if ( isset( $this->parent ) && ! is_int( $this->parent->field_index ) ) {
			return true;
		} else {
			return ! is_int( $this->field_index );
		}

	}
}
