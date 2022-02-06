<?php namespace TeXHyphen;

class PatternDB {

	/**
	 * The storage contains all pattern objects.
	 *
	 * The key is the unique identifier of the pattern, which is
	 * created from the TeX pattern string.
	 *
	 * @var Pattern[]|string
	 */
	private array $storage = [];

	/**
	 * There can be two modes:
	 * - $dataOrFile is an array - creates a whole new hash from an array of TeX pattern
	 *          strings. For the {@link initialize} function the option
	 *          'onlyKeys' and 'sort' can be passed.
	 * - $dataOrFile is a file - loads a serialized hash from a file specified by the
	 *         option 'fileName'. Additional the option 'compression'
	 *         for the {@link unserialize()} can be passed.
	 *
	 * @param array $options Options for the pattern retrieving.
	 */
	public function __construct($dataOrFile, array $options = array()) {
		if (is_array($dataOrFile)) {
			$this->loadFromArray($dataOrFile, $options);
		} else if (is_string($dataOrFile)) {
			$this->loadFormFile($dataOrFile, $options);
		}
	}

	protected function loadFromArray(array $data, array $options) {
		$onlyKeys = $options['onlyKeys'] ?? false;
		$sort = $options['sort'] ?? true;
		$this->initialize($data, $onlyKeys, $sort);
	}

	protected function loadFormFile(string $file, array $options) {
		$compression = $options['compression'] ?? true;
		$data = file_get_contents($file);
		if (false === $data) {
			throw new Exception("Could no open file “{$file}”.");
		}
		$this->unserialize($data, $compression);
	}

	/**
	 * Gets the Pattern object specified by the $key, if it exists in the pattern database.
	 */
	public function getPattern(string $key): ?Pattern {
		if (!isset($this->storage[$key])) {
			return null;
		}

		$pattern = $this->storage[$key];
		if ($pattern instanceof Pattern) {
			return $pattern;
		}
		try {
			$newPattern = new Pattern($pattern);
			$this->storage[$key] = $newPattern;
			return $newPattern;
		} catch (Exception $e) {
			return null;
		}
	}

	/**
	 * Initializes the Pattern object hash.
	 *
	 * The $patternStrArr contains the TeX pattern strings from which
	 * the Pattern will be created. If $onlyKey is true,
	 * only the key of the pattern is created and the TeX pattern
	 * string is included in the hash. The TeXHyphen\Pattern will be
	 * created on demand by getPattern(). If $sort is true the hash
	 * will after initialization sorted by ksort().
	 *
	 * @param array $patternStrArr Array of TeX pattern strings.
	 * @param boolean $onlyKeys Decides, if only the patterns keys are
	 * generated and the TeXHyphen\Pattern on demand.
	 * @param boolean $sort Decides, if the hash is sorted at the end
	 * of the initialization process.
	 *
	 * @see Pattern::createKey()
	 */
	public function initialize(array $patternStrArr, bool $onlyKeys = false, bool $sort = true): void {
		if (empty($patternStrArr)) {
			throw new Exception('Invalid pattern string array');
		}

		foreach ($patternStrArr as $patternStr) {
			if ($onlyKeys) {
				$key = Pattern::createKey($patternStr);
			} else {
				$pattern = new Pattern($patternStr);
				$key = $pattern->getKey();
			}

			if (!isset($this->storage[$key])) {
				if ($onlyKeys) {
					$this->storage[$key] = $patternStr;
				} else {
					$this->storage[$key] = $pattern;
				}
			}
		}

		if ($sort) {
			ksort($this->storage);
		}
	}

	/**
	 * Serialize the current state of the pattern hash and returns
	 * the data.
	 *
	 * Takes the current pattern hash and passes to serialize(). The
	 * returned string, will be compressed by gzcompress(), if
	 * $compression is true.
	 * If the $onlyKeys=true option of the initialize function is used
	 * the missing TeXHyphen\Pattern object will not be created
	 * before serialization.
	 *
	 * @param boolean $compression Decides, if the serialized hash is compressed.
	 * @see initialize()
	 */
	public function serialize(bool $compression = true): string {
		$data = serialize($this->storage);

		if ($compression) {
			$data = gzcompress($data);
		}

		if (false === $data) {
			throw new Exception('Could not compress serialized pattern hash data.');
		}

		return $data;
	}

	/**
	 * Unserialize the pattern hash from the data.
	 *
	 * Takes the $data and decompresses the serialized pattern hash,
	 * if $compression is true. The serialized pattern hash will be
	 * passed to unserialize() and the pattern hash set to the
	 * returned array.
	 *
	 * @param string $data (compressed) serialized hash data.
	 * @param boolean $compression Decides, if the data is
	 * uncompressed before unserialisation of the data.
	 */
	public function unserialize(string $data, bool $compression = true) {
		if ($compression) {
			$data = gzuncompress($data);
		}

		if (false === $data) {
			throw new Exception('Could not decompress serialized pattern hash data.');
		}

		$this->storage = unserialize($data);
		if (false === $this->storage) {
			throw new Exception('Could not unserialize pattern hash data.');
		}
	}
}
