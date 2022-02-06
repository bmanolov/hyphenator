#!/usr/bin/env php
<?php
require __DIR__.'/../vendor/autoload.php';

if (count($argv) === 1) {
	echo <<<usage
Usage:

  $argv[0] <word> [<word2> <word3> ...]

    The hyphenated words are printed to the standard output.

  OR

  $argv[0] <file>

    The source words are read from the given file.
    The hyphenated words are written to a new file with the extension 'hyphenated',
    e.g. if the source file is under /tmp/words then the result is written into /tmp/words.hyphenated

usage;
	exit(-1);
}

function hyphenate_words(array $words): array {
	$hyphenator = new BgHyphenator();
	return $hyphenator->getSyllablesForWords($words);
}

function hyphenate_file(string $file) {
	$words = file($file);
	$hyphenatedWords = hyphenate_words($words);
	$outputFile = "$file.hyphenated";
	file_put_contents($outputFile, implode(PHP_EOL, $hyphenatedWords));
	return $outputFile;
}

if (file_exists($argv[1])) {
	$outputFile = hyphenate_file($argv[1]);
	echo "The hyphenated words were written to the file $outputFile", PHP_EOL;
} else {
	echo implode(PHP_EOL, hyphenate_words(array_slice($argv, 1))), PHP_EOL;
}
