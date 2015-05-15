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
		'DefaultFixUTF8'			=> 'Boolean',
	);

	public function updateCMSFields(\FieldList $fields) {
		$fields->addFieldsToTab('Root.ContentCleaning', array(
			new CheckboxField('ForceAccessibilityChecks', 'Force accessibility checks'),
			new CheckboxField('DefaultTidy', 'Default value for "Tidy"'),
			new CheckboxField('DefaultFixUTF8', 'Default value for "Fix UTF8"setting'),
			new CheckboxField('DefaultPurify', 'Default value for "HTML purify" setting'),
			new CheckboxField('DefaultStripWord', 'Default value for "MS Word strip" setting'),
		));
	}
}
