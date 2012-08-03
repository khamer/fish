<?php
/**
 * Fish is a lightweight routing lirary for PHP 5.
 *
 * @copyright  Kevin Hamer 2012.
 * @author     Kevin Hamer [kh] <kevin@imarc.net>
 * @license    MIT
 *
 * @version    0.1.2
 */

class Fish {

	static private $instances = array();
	static public function instance($name='default', $instance=NULL)
	{
		if ($instance == NULL) {
			if (!isset(self::$instances[$name])) {
				throw new Exception("Tried to load uninitialized instance.");
			}
		} else {
			self::$instances[$name] == $instance;
		}
	}

	static private $symbols = array(
		'%' => '(\d+)',
		'@' => '([:alpha:]+)',
		':' => '(\w+)'
	);

	static public function getRegexFor($syntax)
	{
		foreach (self::$symbols as $symbol => $symbol_regex) {
			$syntax = preg_replace('`' . $symbol . '\w+`', $symbol_regex, $syntax);
		}
		return "`$syntax`";
	}

	static public function getParametersFor($syntax)
	{
		$symbol_list = implode('|', array_keys(self::$symbols));
		preg_match_all("`($symbol_list)(\w+)`", $syntax, $matches);


		return array_combine($matches[0], $matches[2]);
	}

	static public function getValuesFor($syntax, $uri)
	{
		$regex = self::getRegexFor($syntax);
		preg_match_all($regex, $uri, $matches);
		var_dump($matches);
	}

	static public function populateSyntax($syntax, $parameters)
	{
		$used_parameters = self::getParametersFor($syntax);
		foreach ($used_parameters as $exact => $key) {
			if (isset($parameters[$key])) {
				$syntax = str_replace($exact, $parameters[$key], $syntax);
			}
		}

		return $syntax;
	}




	/* INSTANCE */
	private $uri_callback = array();

	public function map($uri, $callback)
	{
		if (is_array($uri)) {
			foreach ($uri as $sub_uri) {
				$this->map($sub_uri, $callback);
			}
		} else {
			$this->uri_callback[$uri] = $view;
		}

		return $this;
	}

	public function go($uri=NULL)
	{
		if (empty($uri)) {
			$uri = $_SERVER['REQUEST_URI'];
		}

		foreach ($this->uri_callback as $current_uri => $current_callback) {
			$uri_regex = self::getRegexFor($current_uri);
			if (preg_match($regex, $uri)) {
				$required_params = self::getParametersFor($current_uri);
				if (count($required_params) <= count(array_intersect($required_params, array_keys($params)))) {
					$
		}
	}
}
