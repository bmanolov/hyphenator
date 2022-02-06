<?php namespace TeXHyphen;

class Pattern {

	/**
	 * The key identifies the pattern.
	 *
	 * The key is a unique identifier, which contains only of
	 * characters and optional a dot at the start or the end. It will be
	 * created from the TeX pattern.
	 *
	 * @see createKey()
	 *
	 * @var string Key by which the pattern is identified.
	 */
	private string $key = '';

	/**
	 * These are the values for the desirability of a hyphen.
	 *
	 * Each array entry values the desirability of a hyphen between
	 * the characters of the pattern. The higher the value the
	 * stronger the desirability of a hyphen. An odd value denotes a
	 * possible hyphen, whereas an even value denotes an impossible
	 * hyphen.
	 * The array is created from the pattern. If there is no number
	 * between two characters, 0 is included.
	 *
	 * @see createHyphenValues()
	 *
	 * @var array Array of integers.
	 */
	private array $hyphenValues = [];

	public function __construct(string $pattern) {
		$pattern = trim($pattern);

		if (!$this->isValid($pattern)) {
			throw new Exception("Pattern “{$pattern}” is invalid.");
		}

		$this->key = self::createKey($pattern);
		$this->hyphenValues = $this->createHyphenValues($pattern);
	}

	/**
	 * Validates a pattern string against some special criteria.
	 *
	 * The pattern string have to meet following criteria:
	 * - not empty
	 * - not only a combination of whitespace characters, digits or dots
	 * - no whitespace character at all
	 * - no dots inside the string
	 *
	 * All other validation for a correct pattern string have to take
	 * place outside the class.
	 * This decision was made, because different languages could have
	 * different characters allowed in pattern strings.
	 *
	 * @param string $pattern TeX pattern string.
	 */
	public function isValid(string $pattern): bool {
		$pattern = preg_replace('!\d!i', '', $pattern);
		$pattern = trim($pattern);

		if (0 === strlen($pattern)) {
			return false;
		}

		// Looks for whitespace characters in trimmed $patternStr.
		if (0 !== preg_match('!\s!i', $pattern)) {
			return false;
		}

		// Checks, if $patternStr contains only of dots.
		if (0 !== preg_match('!^\.+$!i', $pattern)) {
			return false;
		}

		// Checks, if in $patternStr exists dots, but at the start or
		// end. The pattern will only return 0, if a dot in the middle.
		if (0 === preg_match('!^\.?[^.]*\.?$!i', $pattern)) {
			return false;
		}

		return true;
	}

	/**
	 * Creates the key from a TeX pattern string
	 *
	 * The key contains only of the characters and dots of the TeX
	 * pattern string. False will return, if the resulting key
	 * contains only of whitespace characters.
	 *
	 * @param string $pattern TeX pattern string.
	 */
	static public function createKey(string $pattern): string {
		$pattern = trim($pattern);

		$key = preg_replace('!\d!i', '', $pattern);
		$key = trim($key);

		if (0 == strlen($key)) {
			throw new Exception("Empty key for pattern “{$pattern}”.");
		}

		return $key;
	}

	/**
	 * Gets the key of the pattern.
	 *
	 * If the key contains only of whitespace characters false will be returned.
	 *
	 * @return string|false the key by which the pattern can be
	 * identified or false, if the key contains only of whitespace
	 * characters.
	 */
	public function getKey() {
		if (0 == strlen(trim($this->key))) {
			return false;
		}
		return $this->key;
	}

	/**
	 * Creates the hyphen values from a TeX pattern string.
	 *
	 * The hyphen values are an array of integers, which values the
	 * desirability of a hyphen between two characters.
	 *
	 * @param string $pattern TeX pattern string
	 */
	public function createHyphenValues(string $pattern): array {
		$pattern = trim($pattern);

		if (0 == strlen($pattern)) {
			throw new Exception("Empty pattern.");
		}

		$cnt = strlen($pattern);
		$hv = array_fill(0, $cnt + 1, 0);
		$j = 0;
		for ($i = 0; $i < $cnt; $i++) {
			$c = $pattern[$i];
			if (is_numeric($c)) {
				$hv[$j] *= 10;
				$hv[$j] += intval($c);
			} else {
				$j++;
			}
		};
		return $hv;
	}

	/**
	 * Gets the hyphen values of the pattern.
	 *
	 * @return array|false Array of integers or false, if the hyphen
	 * values is empty or not an array.
	 */
	public function getHyphenValues() {
		return $this->hyphenValues;
	}

	/**
	 * Gets the hyphen value at a passed index.
	 *
	 * False will return, if the hyphen value isn't set or the value
	 * isn't an integer.
	 *
	 * @param integer $index Index of the value.
	 *
	 * @return integer|false the value at the index in the hyphen
	 * values or false, if the hyphen values are invalid or the index
	 * isn't available.
	 */
	public function getHyphenValue(int $index) {
		if (!isset($this->hyphenValues[$index]) || (!is_int($this->hyphenValues[$index]))) {
			return false;
		}
		return $this->hyphenValues[$index];
	}

}
