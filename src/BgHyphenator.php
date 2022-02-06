<?php

use TeXHyphen\PatternDB;
use TeXHyphen\TeXHyphen;
use TeXHyphen\WordCache;

class BgHyphenator {
	private TeXHyphen $hyphenator;
	private string $delimiter;

	public function __construct(string $delimiter = '-') {
		$this->hyphenator = $this->constructHyphenator();
		$this->delimiter = $delimiter;
	}

	public function getSyllables($word) {
		if (empty($word) || $word == '—' || strpos($word, ' ') !== false) {
			return $word;
		}

		$syls = $this->hyphenator->getSyllables($this->hyphenEncode($word));

		// check for incorrect hyphenation: a leading syllable of one letter only
		if (strlen($syls[0]) == 1 && count($syls) > 1) {
			$syls[1] = $syls[0] . $syls[1];
			unset($syls[0]);
		}

		$hyphenWord = implode($this->delimiter, $syls);
		// remove double hyphens
		$hyphenWord = str_replace($this->delimiter . $this->delimiter, $this->delimiter, $hyphenWord);

		return $this->hyphenDecode($hyphenWord);
	}

	public function getSyllablesForWords(array $words): array {
		$hyphenatedWords = array_map(function(string $word) {
			return $this->getSyllables($word);
		}, $words);
		return array_combine($words, $hyphenatedWords);
	}

	private function hyphenEncode($word) {
		return iconv('UTF-8', 'windows-1251', $word);
	}

	private function hyphenDecode($word) {
		return iconv('windows-1251', 'UTF-8', $word);
	}

	private function constructHyphenator(): TeXHyphen {
		// create a pattern source by loading a pattern file
		$patterns = file(__DIR__ . '/../data/hyph_bg_BG.tex');
		// remove header line with source information
		array_shift($patterns);
		return new TeXHyphen(new PatternDB($patterns, ['onlyKeys' => true]), new WordCache());
	}
}
