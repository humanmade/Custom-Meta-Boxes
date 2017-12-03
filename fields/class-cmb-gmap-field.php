<?php
/**
 * Google map field class for CMB
 *
 * It enables the google places API and doesn't store the place
 * name. It only stores latitude and longitude of the selected area.
 *
 * Note - you need a Google API key for field to work correctly.
 *
 * @since 1.0.2
 *
 * @extends CMB_Field
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

class CMB_Gmap_Field extends CMB_Field {

	/**
	 * Get default arguments for field including custom parameters.
	 *
	 * @return array Default arguments for field.
	 */
	public function get_default_args() {
		return array_merge(
			parent::get_default_args(),
			array(
				'field_width'                 => '100%',
				'field_height'                => '250px',
				'default_lat'                 => '51.5073509',
				'default_long'                => '-0.12775829999998223',
				'default_zoom'                => '8',
				'string-marker-title'         => esc_html__( 'Drag to set the exact location', 'cmb' ),
				'string-gmaps-api-not-loaded' => esc_html__( 'Google Maps API not loaded.', 'cmb' ),
				'google_api_key'              => '',
				'language'                    => explode( '_', get_locale() )[0],
			)
		);
	}

	/**
	 * Enqueue all scripts required by the field.
	 *
	 * @uses wp_enqueue_script()
	 */
	public function enqueue_scripts() {

		parent::enqueue_scripts();

		wp_enqueue_script( 'cmb-google-maps-script', trailingslashit( CMB_URL ) . 'js/field-gmap.js', array( 'jquery' ), CMB_VERSION );

		// Check for our key with either a field argument or constant.
		$key = '';
		if ( ! empty( $this->args['google_api_key'] ) ) {
			$key = $this->args['google_api_key'];
		} elseif ( defined( 'CMB_GAPI_KEY' ) ) {
			$key = CMB_GAPI_KEY;
		}

		wp_localize_script( 'cmb-google-maps-script', 'CMBGmaps', array(
			'key'      => $key,
			'defaults' => array(
				'latitude'  => $this->args['default_lat'],
				'longitude' => $this->args['default_long'],
				'zoom'      => $this->args['default_zoom'],
			),
			'strings'  => array(
				'markerTitle'            => $this->args['string-marker-title'],
				'googleMapsApiNotLoaded' => $this->args['string-gmaps-api-not-loaded'],
			),
			'language' => $this->args['language'],
		) );

	}

	/**
	 * Get multiple values for a field.
	 *
	 * @return array
	 */
	public function get_values() {
		return ( ! $this->args['repeatable'] ) ? array( $this->values ) : $this->values;
	}

	/**
	 * Print out field HTML.
	 */
	public function html() {

		// Ensure all args used are set.
		$value = wp_parse_args(
			$this->get_value(),
			array(
				'lat'       => null,
				'long'      => null,
				'elevation' => null,
				'text'      => null,
			)
		);

		$style = array(
			sprintf( 'width: %s;', $this->args['field_width'] ),
			sprintf( 'height: %s;', $this->args['field_height'] ),
			'border: 1px solid #eee;',
			'margin-top: 8px;',
		);

		?>

		<input type="text" <?php $this->class_attr( 'map-search' ); ?> <?php $this->id_attr(); ?> <?php $this->name_attr( '[text]' ); ?> value="<?php echo esc_attr( $value['text'] ); ?>" />

		<div class="map" style="<?php echo esc_attr( implode( ' ', $style ) ); ?>"></div>

		<input type="hidden" class="latitude"  <?php $this->name_attr( '[lat]' ); ?>       value="<?php echo esc_attr( $value['lat'] ); ?>" />
		<input type="hidden" class="longitude" <?php $this->name_attr( '[long]' ); ?>      value="<?php echo esc_attr( $value['long'] ); ?>" />
		<input type="hidden" class="elevation" <?php $this->name_attr( '[elevation]' ); ?> value="<?php echo esc_attr( $value['elevation'] ); ?>" />

		<?php
	}
}
