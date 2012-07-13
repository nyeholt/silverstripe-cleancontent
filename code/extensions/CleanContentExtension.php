<?php

/**
 * Allow users to tidy + purify content when saved
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class CleanContentExtension extends DataExtension {

	static $db = array(
		'TidyHtml'			=> 'Boolean',
		'PurifyHtml'		=> 'Boolean',
		'CleanOnSave'		=> 'Boolean',
	);


	static $defaults = array(
		'CleanOnSave'		=> true,
		'TidyHtml'			=> true,
		'PurifyHtml'		=> false,
	);

	
	public function updateCMSFields(FieldList $fields) {
		$options = new FieldGroup(
			new CheckboxField('TidyHtml', _t('TidyContent.TIDY_HTML', 'Tidy HTML')),
			new CheckboxField('PurifyHtml', _t('TidyContent.PURIFY_HTML', 'Purify HTML')),
			new CheckboxField('CleanOnSave', _t('TidyContent.CLEAN_ON_SAVE', 'Clean on Save'))
		);
		$options->setTitle('Cleaning:');
		$fields->addFieldToTab('Root.Cleaning', $options);
	}
	
	
	public function onBeforeWrite() {
		if ($this->owner->CleanOnSave && $this->owner->isChanged('Content')) {
			$this->owner->Content = $this->Clean()->getValue();
		}
	}


	/**
	 * Usable from templates if CleanOnSave isn't applicable
	 */
	public function Clean($field = 'Content') {
		$content = $this->owner->$field;

		if ($this->owner->PurifyHtml) {
			$content = singleton('CleanContentService')->purify($content);
		}

		if ($this->owner->TidyHtml) {
			$content = singleton('CleanContentService')->tidy($content);
		}

		return DBField::create_field('HTMLVarchar', $content);
	}
}
