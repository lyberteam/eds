<?php
/**
 * Eds example
 *
 * Eds config is loaded from a file: eds.conf.php
 *
 */

/**
 * Define paths
 */
define('CONF_FILE', dirname(__FILE__).'/'.'eds.conf.php');
define('EDS_LIB_DIR', dirname(dirname(__FILE__)).'/lib/Eds/');

/**
 * Load config
 */
if (!file_exists(CONF_FILE)) {
    trigger_error('Config file missing at '.CONF_FILE, E_USER_ERROR);
    exit();
}
require CONF_FILE;

/**
 * Instantiate Eds with the loaded config
 */
require EDS_LIB_DIR.'Eds.php';
$Eds = new Eds( $config );