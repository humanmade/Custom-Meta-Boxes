<?php
/**
 * Tests for the single checkox field type.
 *
 * @since 1.1.0
 *
 * @package WordPress
 * @subpackage Custom Meta Boxes
 */

namespace HMCMB\Tests;

use CMB_Checkbox;

/**
 * Class CheckboxFieldTestCase
 *
 * @group fields
 */
class CheckboxFieldTestCase extends TestFieldCase {
	public function setUp() {
		parent::setUp();

		$this->instance = new CMB_Checkbox( 'CMB_Checkbox', 'Field', [] );
	}
}
