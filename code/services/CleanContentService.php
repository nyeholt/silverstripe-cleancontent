<?php
use \ForceUTF8\Encoding;
/**
 * Service responsible for cleaning up content using purify and html tidy
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class CleanContentService {

	public static $accessibility_template = "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01//EN\" \"http://www.w3.org/TR/html4/strict.dtd\"><html><head><title>Dummy</title><meta name=\"DCTERMS.title\" content=\"Home\" /></head><body>\$content</body></html>";
	public static $ignore_errors = array(
		'line 1 column 1 - Access: [3.3.1.1]: use style sheets to control presentation.',
		'Access: [1.1.2.1]: <img> missing \'longdesc\' and d-link.',
		"Accessibility Checks: Version 0.1",
		'proprietary attribute "aria-describedby"',
		"errors were found",
		"Warning: <a> escaping malformed URI reference",
		'style sheets require testing',
		'remove flicker (animated gif)',
	);
	public $purifier;
	public $defaultPurifyOptions = array();

	public function __construct() {
//		include_once dirname(dirname(__FILE__)) . '/thirdparty/htmlpurifier-4.4.0-lite/library/HTMLPurifier.auto.php';
		$config = HTMLPurifier_Config::createDefault();
		$this->purifier = new HTMLPurifier($config);
	}

	public function accessible($content, $stripWord = false) {
		if (extension_loaded('tidy')) {
			$check = str_replace('$content', $content, self::$accessibility_template);
			$tidy = tidy_parse_string($check, array(
				'clean' => true,
				'doctype' => 'omit',
				'show-body-only' => true,
				'wrap' => 0,
				'input-encoding' => 'utf8',
				'output-encoding' => 'utf8',
				'new-blocklevel-tags' => 'article aside audio details figcaption figure footer header hgroup nav section source summary temp track video',
				'new-empty-tags' => 'command embed keygen source track wbr',
				'new-inline-tags' => 'audio canvas command datalist embed keygen mark meter output progress time video wbr',
				'accessibility-check' => 2,
//				'bare'				=> $stripWord,
				'word-2000' => $stripWord
			));

			$tidy->cleanRepair();
			$tidy->diagnose();

			$info = preg_replace('/Info: (.*?\n)/is', '', $tidy->errorBuffer);

			$lines = explode("\n", $info);
			$good = array();
			foreach ($lines as $line) {
				$ignore = false;
				foreach (self::$ignore_errors as $toIgnore) {
					if (strpos($line, $toIgnore) !== false) {
						$ignore = true;
						continue;
					}
				}
				if (!$ignore) {
					$good[] = preg_replace('/line \d+ column \d+ (.*?)\d+\.\d+\]:/', '', $line);
				}
			}

			$info = implode("\n", $good);

			return trim($info);
		}
	}

	public function tidy($content, $stripWord = false) {

		// Try to use the extension first
		if (extension_loaded('tidy')) {

			$tidy = tidy_parse_string($content, array(
				'clean' => true,
				'output-xhtml' => true,
				'show-body-only' => true,
				'wrap' => 0,
				'input-encoding' => 'utf8',
				'output-encoding' => 'utf8',
				'new-blocklevel-tags' => 'article aside audio details figcaption figure footer header hgroup nav section source summary temp track video',
				'new-empty-tags' => 'command embed keygen source track wbr',
				'new-inline-tags' => 'audio canvas command datalist embed keygen mark meter output progress time video wbr',
				'bare'				=> $stripWord,
				'word-2000' => $stripWord
			));

			$tidy->cleanRepair();
			return $this->rewriteShortcodes('' . $tidy);
		}

		// No PHP extension available, attempt to use CLI tidy.
		$retval = null;
		$output = null;
		@exec('tidy --version', $output, $retval);
		if ($retval === 0) {
			$tidy = '';
			$input = escapeshellarg($content);
			// Doesn't work on Windows, sorry, stick to the extension.
			$tidy = @`echo $input | tidy -q --show-body-only yes --input-encoding utf8 --output-encoding utf8 --wrap 0 --clean yes --output-xhtml yes`;
			return $this->rewriteShortcodes($tidy);
		}

		// Fall back to default
		$doc = new SS_HTML4Value($content);
		return $doc->getContent();
	}

	/**
	 * removes %20 from shordcodes inside a href attr
	 */
	protected function rewriteShortcodes($string) {
		return preg_replace('/(\[[^]]*?)(%20)([^]]*?\])/m', '$1 $3', $string);
	}

	/**
	 * Run html purifier over the content
	 *
	 * @param string $content
	 * @return string
	 */
	public function purify($content, $options = null) {
		if (!$options) {
			$options = $this->defaultPurifyOptions;
		}

		$content = $this->purifier->purify($content, $options);
		$content = preg_replace_callback('/\%5B(.*?)\%5D/', array($this, 'reformatShortcodes'), $content);
		return $content;
	}
	
	public function fixUtf8($content) {
		 $content = Encoding::fixUTF8($content);
		 $content = str_replace('Â', '', $content);
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
