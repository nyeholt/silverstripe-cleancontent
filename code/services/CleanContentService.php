<?php

/**
 * Service responsible for cleaning up content using purify and html tidy
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class CleanContentService {

	private $purifier;
	
	public function __construct() {
		include_once dirname(dirname(__FILE__)) . '/thirdparty/htmlpurifier-4.0.0-lite/library/HTMLPurifier.auto.php';
		$this->purifier = new HTMLPurifier();
	}

	public function tidy($content) {
		// Try to use the extension first
		if (extension_loaded('tidy')) {
			$tidy = tidy_parse_string($content, array(
				'clean'				=> true,
				'output-xhtml'		=> true,
				'show-body-only'	=> true,
				'wrap'				=> 0,
				'input-encoding'	=> 'utf8',
				'output-encoding'	=> 'utf8'
			));

			$tidy->cleanRepair();
			return '' . $tidy;
		}

		// No PHP extension available, attempt to use CLI tidy.
		$retval = null;
		$output = null;
		@exec('tidy --version', $output, $retval);
		if ($retval === 0) {
			$input = escapeshellarg($content);
			// Doesn't work on Windows, sorry, stick to the extension.
			$tidy = @`echo $input | tidy -q --show-body-only yes --input-encoding utf8 --output-encoding utf8 --wrap 0 --clean yes --output-xhtml yes`;
			return $tidy;
		}

		// Fall back to default
		$doc = new SS_HTMLValue($content);
		return $doc->getContent();
	}

	/**
	 * Run html purifier over the content
	 *
	 * @param string $content
	 * @return string
	 */
	public function purify($content, $options = null) {
		$content = $this->purifier->purify($content);
		$content = preg_replace_callback('/\%5B(.*?)\%5D/', array($this, 'reformatShortcodes'), $content);
		return $content;
	}

	/**
	 * Reformats shortcodes after being run through htmlpurifier
	 *
	 * @param array $matches
	 */
	public function reformatShortcodes($matches) {
		$val = urldecode($matches[1]);
		return '[' . $val . ']';
	}
}
