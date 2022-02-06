<?php namespace TeXHyphen;

class WordCache {
	/**
	 * The key is the word of which the syllables are stored
	 * @var array Array of array of strings.
	 */
	private array $syllables = [];

	/**
	 * Gets the syllables of a word, if found in cache.
	 *
	 * @return array|false Array of string or false, if $word isn't found.
	 */
	public function getSyllables(string $word) {
		$key = strtolower($word);
		if (!isset($this->syllables[$key])) {
			return false;
		}

		$syls = $this->syllables[$key];
		if (0 !== strncmp($word, $syls[0], 1)) {
			$syls[0] = ucfirst($syls[0]);
		}

		return $syls;
	}

	/**
	 * Adds a word and its syllables to the cache.
	 *
	 * @param string $word Word, which syllables should be stored.
	 * @param array $syls Array of strings, which contains of the
	 * syllables of the $word.
	 *
	 * @return boolean true, if the $word could be added to the cache, otherwise false.
	 */
	public function add(string $word, array $syls): bool {
		$key = strtolower($word);
		if (!isset($this->syllables[$key])) {
			$syls[0] = strtolower($syls[0]);
			$this->syllables[$word] = $syls;
		}
		return true;
	}

}
