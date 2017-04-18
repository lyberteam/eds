<?php
/**
 * EdsTest
 *
 * @package      Eds.EdsTest
 * @license      WTFPL License
 */

require './lib/Eds/Eds.php';

/**
 * OpauthTest class
 */
class EdsTest extends PHPUnit_Framework_TestCase {

	protected function setUp() {
		// To surpress E_USER_NOTICE on missing $_SERVER indexes
		$_SERVER['HTTP_HOST'] = 'example1.com';
		$_SERVER['REQUEST_URI'] = '/';
	}

	public function testConstructor() {
		$Eds = self::instantiateEdsForTesting();
		$this->assertEquals($Eds->env['host'], 'http://example.com');
		$this->assertEquals($Eds->env['path'], '/');
		$this->assertEquals($Eds->env['request_uri'], '/');


		$Eds = self::instantiateEdsForTesting(array(
			'host' => 'http://example2.com',
		));
		$this->assertEquals($Eds->env['host'], 'http://example2.com');
	}

	protected static function configForTest($config = array()) {
		return array_merge(array(
			'host' => 'http://example.com',
			'path' => '/',
			'strategy_dir' => dirname(__FILE__).'/Strategy/',
			'Strategy' => array(
				'Sample' => array(
					'delimiter' => '|'
				)
			)
		), $config);
	}

	/**
	 * @param array $config Config changes to be merged with the default
	 * @param boolean $autoRun Should Eds be run right after instantiation, defaulted to false
	 * @return object Eds instance
	 */
	protected static function instantiateEdsForTesting($config = array(), $autoRun = false) {
		$Eds = new Eds(self::configForTest($config), $autoRun);
		return $Eds;
	}

}