<?php
/**
 * First strategy for Eds
 * @package      Eds.FirstStrategy
 * @license      WTFPL License
 */

class FirstStrategy extends EdsStrategy{

	/**
	 * Show some hash
     */
	public function show_hash(){
		if (array_key_exists('string', $_GET) && !empty($_GET['string'])){
			$params = array(
				'string' => trim($_GET['string'])
			);
			echo "Privet ". ucfirst ( $this->env['params']['strategy'] . " | " . $params['string']  );
			}
			else{
				echo "no test string";
			}

	}

	/**
	 * Show some hash
	 */
	public function show_another_hash(){
		echo "This is another hash ". ucfirst ( $this->env['params']['strategy'] );
	}
}