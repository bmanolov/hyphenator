<?php

class BgHyphenatorTest extends \PHPUnit\Framework\TestCase {

	protected BgHyphenator $hyphenator;

	/** @dataProvider data_hyphenate */
	public function test_hyphenate(string $word, string $expectedSyllables) {
		$result = $this->hyphenator()->getSyllables($word);
		$this->assertSame($expectedSyllables, $result);
	}
	public function data_hyphenate(): array {
		return [
			['думата', 'ду-ма-та'],
			['яйцето', 'яй-це-то'],
			['пеперудка', 'пе-пе-руд-ка'],
		];
	}

	protected function hyphenator(): BgHyphenator {
		return $this->hyphenator ??= new BgHyphenator();
	}

}
