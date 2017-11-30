<?php

namespace HMCMB\Tests;

use CMB_Taxonomy;

/**
 * Class TaxonomyFieldTestCase
 *
 * @group fields
 */
class TaxonomyFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Taxonomy( 'field', 'Field', [] );
	}
}
