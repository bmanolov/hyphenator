<?php namespace TeXHyphen;

class TeXHyphen {
	/**
	 * Minimum characters before the first hyphen.
	 */
	private int $leftHyphenMin = 1;

	/**
	 * Minimum characters after the last hyphen.
	 */
	private int $rightHyphenMin = 2;

	private PatternDB $patternDB;

	/**
	 * Cache of hyphenated words.
	 */
	private ?WordCache $wordCache;

	public function __construct(PatternDB $patternDB, ?WordCache $wordCache = null) {
		$this->patternDB = $patternDB;
		$this->wordCache = $wordCache;
	}

	/**
	 * Gets the syllables of a word.
	 *
	 * If no syllables ar found then also an array with the passed
	 * word is returned.
	 *
	 * @param string $word Word of which the syllables should
	 * calculated.
	 *
	 * @return array Array of string, if $word contains non word
	 * characters, the word will be returned unchanged.
	 */
	public function getSyllables(string $word): array {
		$word = trim($word);

		/* can cause errors, because of language settings of the server
		if (0 != preg_match('![^\w]+!i', $word)) {
			return array($word);
		}
		*/

		$wordLen = strlen($word);

		if ($wordLen <= ($this->leftHyphenMin + $this->rightHyphenMin) ) {
			return array($word);
		}

		if ($this->wordCache) {
			$cachedWord = $this->wordCache->getSyllables($word);
			if (false !== $cachedWord) {
				return $cachedWord;
			}
		};

		$hyphenValues = $this->getHyphenValues($word);
		$sylArr = array();
		$syl = '';
		for ($i = 0; $i < $wordLen; $i++) {
			if (($i >= $this->leftHyphenMin) &&
			($i <= ($wordLen - $this->rightHyphenMin)) &&
			($hyphenValues[$i] % 2 == 1) ) {
				$sylArr[] = $syl;
				$syl = '';
			};
			$syl .= $word[$i];
		};
		$sylArr[] = $syl;

		if ($this->wordCache) {
			$this->wordCache->add($word, $sylArr);
		}

		return $sylArr;
	}

	/**
	 * Gets the desirability of a hyphen between the characters.
	 *
	 * Parses the $word for patterns and sets desirability of a hyphen
	 * between characters, which depends on the found pattern.
	 *
	 * @param string $word Word of which the hyphen values should be found.
	 *
	 * @return array Array of integers with the desirability of a hyphen.
	 */
	private function getHyphenValues(string $word): array {
		$word = strtolower(".".$word.".");
		$wordLen = strlen($word);
		$values = array_fill(0, $wordLen + 1, 0);
		for ($i = 0; $i < $wordLen; $i++) {
			$keyStr = '';
			for ($j = 0; ($i + $j) < $wordLen; $j++ ) {
				$keyStr .= $word[$i+$j];
				if ($pattern = $this->patternDB->getPattern($keyStr)) {
					$hv = $pattern->getHyphenValues();
					$keyLen = strlen($keyStr);
					for ($k = 0; $k <= $keyLen; $k++) {
						$values[$i+$k] = max($hv[$k], $values[$i+$k]);
					};
				};
			};
		};
		array_shift($values);
		return $values;
	}

}
