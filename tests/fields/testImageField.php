<?php

namespace HMCMB\Tests;

use CMB_Image_Field;

/**
 * Class ImageFieldTestCase
 *
 * @group fields
 */
class ImageFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Image_Field( 'field', 'Field', [] );
	}
}
