<?php

namespace HMCMB\Tests;

use CMB_Title;

/**
 * Class TitleFieldTestCase
 *
 * @group fields
 */
class TitleFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Title( 'field', 'Field', [] );
	}
}
