<?php

namespace HMCMB\Tests;

use CMB_Gmap_Field;

/**
 * Class GmapFieldTestCase
 *
 * @group fields
 */
class GmapFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Gmap_Field( 'field', 'Field', [] );
	}
}
