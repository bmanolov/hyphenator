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
			['', ''],
			['думата', 'ду-ма-та'],
			['яйцето', 'яй-це-то'],
			['пеперудка', 'пе-пе-руд-ка'],
			['самосвал', 'са-мос-вал'],
			['издигам', 'из-ди-гам'],
		];
	}

	public function test_getSyllablesForWords() {
		$words = ['самосвал', 'бързовар'];
		$expected = ['самосвал' => 'са-мос-вал', 'бързовар' => 'бър-зо-вар'];
		$result = $this->hyphenator()->getSyllablesForWords($words);
		$this->assertSame($expected, $result);
	}

	protected function hyphenator(): BgHyphenator {
		return $this->hyphenator ??= new BgHyphenator();
	}
}
