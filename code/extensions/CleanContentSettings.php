<?php

/**
 * 
 *
 * @author <marcus@silverstripe.com.au>
 * @license BSD License http://www.silverstripe.org/bsd-license
 */
class CleanContentSettings extends DataExtension {
	private static $db = array(
		'ForceAccessibilityChecks'	=> 'Boolean',
		'DefaultTidy'				=> 'Boolean',			// default base URL to cache for, regardless of curent base
		'DefaultPurify'				=> 'Boolean',
		'DefaultStripWord'			=> 'Boolean',
	);

	public function updateCMSFields(\FieldList $fields) {
		$fields->addFieldsToTab('Root.ContentCleaning', array(
			new CheckboxField('ForceAccessibilityChecks', 'Force accessibility checks'),
			new CheckboxField('DefaultTidy', 'Default tidy setting'),
			new CheckboxField('DefaultPurify', 'Default purify setting'),
			new CheckboxField('DefaultStripWord', 'Default MS Word strip setting'),
		));
	}
}
