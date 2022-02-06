A helper to hyphenate Bulgarian words.

## Usage in PHP code

```php
$word = 'котарак';

$hyphenator = new BgHyphenator();
echo $hyphenator->getSyllables($word);// ко-та-рак

$hyphenator2 = new BgHyphenator('~');
echo $hyphenator2->getSyllables($word);// ко~та~рак

$words = ['котарак', 'самосвал'];
echo implode(", ", $hyphenator->getSyllablesForWords($words)); // ко-та-рак, са-мос-вал
```

## Usage with a shell script

    bin/hyphenate.php <word> [<word2> <word3> ...]

  The hyphenated words are printed to the standard output.

OR

    bin/hyphenate.php <file>

  The source words are read from the given file.
  The hyphenated words are written to a new file with the extension 'hyphenated',
  e.g. if the source file is under /tmp/words then the result is written into /tmp/words.hyphenated
