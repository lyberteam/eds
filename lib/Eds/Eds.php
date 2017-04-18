<?php
/**
 * Eds
 * Multi-provider authentication framework for PHP
 *
 * @package      Eds
 * @license      WTFPL License
 */

/**
 * Eds
 * @package			Eds
 */
class Eds {
    /**
     * settings
     */
    public $config;

    /**
     * Environment variables
     */
    public $env;

    /**
     * Strategy map: for mapping URL-friendly name to Class name
     */
    public $strategyMap;

    /**
     * Constructor
     * Loads user configuration and strategies.
     *
     * @param array $config User configuration
     * @param boolean $run Whether Eds should auto run after initialization.
     */
    public function __construct($config = array(), $run = true) {
        /**
         * Environment variables, including config
         * Used mainly as accessors
         */
        $this->env = array_merge(array(
            'path' => '/',
            'request_uri' => $_SERVER['REQUEST_URI'],
            'lib_dir' => dirname(__FILE__).'/',
            'strategy_dir' => dirname(__FILE__).'/Strategy/'
        ), $config);

        if (!class_exists('EdsStrategy')) {
            require $this->env['lib_dir'].'EdsStrategy.php';
        }
        $this->loadStrategies();

        if ($run) {
            $this->run();
        }
    }

    /**
     * Run Eds:
     * Parses request URI and perform defined actions based based on it.
     */
    public function run() {
        $this->parseUri();

        if (!empty($this->env['params']['strategy'])) {
            if (array_key_exists($this->env['params']['strategy'], $this->strategyMap)) {
                $name = $this->strategyMap[$this->env['params']['strategy']]['name'];
                $class = $this->strategyMap[$this->env['params']['strategy']]['class'];
                $strategy = $this->env['Strategy'][$name];

                // Strip out critical parameters
                $safeEnv = $this->env;
                unset($safeEnv['Strategy']);

                $actualClass = $this->requireStrategy($class);
                $this->Strategy = new $actualClass($strategy, $safeEnv);

                if (empty($this->env['params']['action'])) {
                    $this->env['params']['action'] = 'request';
                }

                $this->Strategy->callAction($this->env['params']['action']);
            } else {
                trigger_error('No strategy with name - '.$this->env['params']['strategy'], E_USER_ERROR);
            }
        } else {
            $sampleStrategy = array_pop($this->env['Strategy']);
            trigger_error('No strategy is requested. '.$sampleStrategy['strategy_url_name'].'  with '.$sampleStrategy['strategy_name'], E_USER_NOTICE);
        }
    }

    /**
     * Parses Request URI
     */
    private function parseUri() {
        $this->env['request'] = substr($this->env['request_uri'], strlen($this->env['path']) - 1);

        if (preg_match_all('/\/([A-Za-z0-9-_]+)/', $this->env['request'], $matches)) {
            foreach ($matches[1] as $match) {
                $this->env['params'][] = $match;
            }
        }

        if (!empty($this->env['params'][0])) {
            $this->env['params']['strategy'] = $this->env['params'][0];
        }
        if (!empty($this->env['params'][1])) {
            $this->env['params']['action'] = $this->env['params'][1];
        }
    }

    /**
     * Load strategies
     */
    private function loadStrategies() {
        if (isset($this->env['Strategy']) && is_array($this->env['Strategy']) && count($this->env['Strategy']) > 0) {
            foreach ($this->env['Strategy'] as $key => $strategy) {
                if (!is_array($strategy)) {
                    $key = $strategy;
                    $strategy = array();
                }

                $strategyClass = $key;
                if (array_key_exists('strategy_class', $strategy)) {
                    $strategyClass = $strategy['strategy_class'];
                } else {
                    $strategy['strategy_class'] = $strategyClass;
                }

                $strategy['strategy_name'] = $key;

                // Define a URL-friendly name
                if (empty($strategy['strategy_url_name'])) {
                    $strategy['strategy_url_name'] = strtolower($key);
                }

                $this->strategyMap[$strategy['strategy_url_name']] = array(
                    'name' => $key,
                    'class' => $strategyClass
                );

                $this->env['Strategy'][$key] = $strategy;
            }
        } else {
            trigger_error('No defined Eds strategies', E_USER_ERROR);
        }
    }

    /**
     * Loads a strategy
     * @param string $strategy  Strategy name
     * @return string strategy  Class name
     */
    private function requireStrategy( $strategy) {
        if (!class_exists($strategy.'Strategy')) {
            // Include dir
            // specifying a dir name, eg. eds-first
            $directories = array(
                $this->env['strategy_dir'].$strategy.'/',
                $this->env['strategy_dir'].'eds-'.strtolower($strategy).'/',
                $this->env['strategy_dir'].strtolower($strategy).'/',
                $this->env['strategy_dir'].'Eds-'.$strategy.'/'
            );

            // Include deprecated support for strategies without Strategy postfix as class name or filename
            $classNames = array(
                $strategy.'Strategy',
                $strategy
            );

            foreach ($directories as $dir) {
                foreach ($classNames as $name) {
                    if (file_exists($dir.$name.'.php')) {
                        require $dir.$name.'.php';
                        return $name;
                    }
                }
            }

            trigger_error('Strategy class file ('.$this->env['strategy_dir'].$strategy.'/'.$strategy.'Strategy.php'.') is missing', E_USER_ERROR);
        }
        return $strategy.'Strategy';
    }
}
