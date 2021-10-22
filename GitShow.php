<?php
namespace winternet\phpGit;

class GitShow {

	public $parsedData = [];

	public $propertyBuffers = [];

	/**
	 * Parse the output of `git show`
	 *
	 * To eg. retrieve the last 999 commits you do `git show -999`.
	 *
	 * @param string $stdout Output from git command
	 * @return array
	 */
	public function parse($stdout) {
		$lines = explode("\n", $stdout);

		$this->parsedData = $this->propertyBuffers = [];
		$currProperty = null;
		foreach ($lines as $line) {
			$line = trim($line, "\r") ."\n";

			if (preg_match("/^commit ([a-f0-9]{40})/", $line, $match) && (!$currProperty || $currProperty == 'unifiedDiff')) {  //must be first or come after unifiedDiff
				$currProperty = 'hash';
				$line = $match[1];

				$this->saveBuffer();

			} elseif (preg_match("/^Author: (.*)/", $line, $match) && $currProperty == 'hash') {  //must come after hash
				$currProperty = 'author';
				$line = trim($match[1]);

			} elseif (preg_match("/^Date: (.*)/", $line, $match) && $currProperty == 'author') {  //must come after author
				$currProperty = 'timestamp';
				$line = trim($match[1]);

			} elseif ($currProperty == 'timestamp') {  //must come after timestamp
				$currProperty = 'message';

			} elseif (preg_match("/^diff --git/", $line, $match) && $currProperty == 'message') {  //must come after message
				$currProperty = 'unifiedDiff';

				$this->propertyBuffers['message'] = trim($this->propertyBuffers['message']);
			}

			if ($currProperty == 'message') {
				$line = preg_replace("/^\\s{4}/", '', $line);
			}

			$this->propertyBuffers[$currProperty] .= $line;
		}

		$this->saveBuffer();

		return $this->parsedData;
	}

	protected function saveBuffer() {
		if (!empty($this->propertyBuffers)) {
			// Store the previous record and prepare for new
			$this->parsedData[] = $this->propertyBuffers;
			$this->propertyBuffers = [];
		}
	}

}
