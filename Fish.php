<?php
/**
 * Fish is a lightweight routing lirary for PHP 5.
 *
 * @copyright  Kevin Hamer 2012.
 * @author     Kevin Hamer [kh] <kevin@imarc.net>
 * @license    MIT
 *
 * @version    0.1.1
 */

class Fish {

	static private $symbols = array(
		'%' => '(\d+)',
		'@' => '([:alpha:]+)',
		':' => '(\w+)'
	);

	static public function regexifyPattern($pattern)
	{
		foreach (self::$symbols as $symbol => $sym_pattern) {
			$pattern = preg_replace('|' . $symbol . '\w+|', $sym_pattern, $pattern);
		}
		return "|$pattern|";
	}

	static public function getUsedParameters($pattern)
	{
		$symbol_list = implode('', array_keys(self::$symbols));
		preg_match_all('|[' . $symbol_list . '](\w+)|', $pattern, $matches);

		return array_combine($matches[1], $matches[0]);
	}

	static public function populatePattern($pattern, $data)
	{
		$parameters = self::getUsedParameters($pattern);
		foreach ($parameters as $key => $exact) {
			$pattern = str_replace($exact, $data[$key], $pattern);
		}

		return $pattern;
	}

	/*
	 * Syntaxes accepted:
	 *   * place => view
	 *   * array(place, place, ...) => view
	 */
	public function map($request, $view)
	{
		if (is_array($request)) {
			foreach ($request as $req) {
				$this->request_view[$req] = $view;
			}
		} else {
			$this->request_view[$request] = $view;
		}
		return $this;
	}

	private $request_view = array();

	public function go($pattern=NULL)
	{
		foreach ($this->request_view as $request_pattern => $view_callback) {
			$regex = self::regexifyPattern($request_pattern);
			if (preg_match($regex, $pattern)) {
				echo "$pattern goes to $view_callback\n";
				return $view_callback;
			}
		}
	}

	public function link($data, $view)
	{
		foreach ($this->request_view as $request_pattern => $view_callback) {
			$regex = self::regexifyPattern($view_callback);
			if (preg_match($regex, $view)) {
				$used_params = self::getUsedParameters($request_pattern);
				if (count($used_params) <= count(array_intersect_key($used_params, $data))) {
					$populated = self::populatePattern($request_pattern, $data);
					echo json_encode($data) . "(view=$view) links to $populated\n";
					return $populated;
				}
			}
		}
	}
}

/* Tests */
$fish = new Fish();

$fish
	->map(['/users/%id/@method', '/users/%id', '/users'],  'Users')
	->map('/portfolio',                                    'Portfolio')
	->map('/process',                                      'Process');

$fish->go('/users/23');

$fish->link([], 'Users');
$fish->link(['id' => 42], 'Users');
$fish->link(['id' => 42, 'method' => 'party'], 'Users');
