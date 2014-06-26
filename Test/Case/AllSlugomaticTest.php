<?php
/**
 * All Slugomatic plugin tests
 */
class AllSlugomaticTest extends CakeTestCase {

/**
 * Suite define the tests for this plugin
 *
 * @return void
 */
	public static function suite() {
		$suite = new CakeTestSuite('All Slugomatic test');

		$path = CakePlugin::path('Slugomatic') . 'Test' . DS . 'Case' . DS;
		$suite->addTestDirectoryRecursive($path);

		return $suite;
	}

}