<?php

/**
 * Allow users to tidy + purify content when saved
 *
 * @author marcus@silverstripe.com.au
 * @license BSD License http://silverstripe.org/bsd-license/
 */
class CleanContentExtension extends DataObjectDecorator {
	
	static $clean_on_save = true;
	static $default_tidy = true;
	static $default_purify = false;
	
	public function extraStatics() {
		$extra = array(
			'db'			=> array(
				'TidyHtml'			=> 'Boolean',
				'PurifyHtml'		=> 'Boolean',
				'CleanOnSave'		=> 'Boolean',
			),
			'defaults'		=> array(
				'CleanOnSave'		=> self::$clean_on_save,
				'TidyHtml'			=> self::$default_tidy,
				'PurifyHtml'		=> self::$default_purify,
			)
		);

		return $extra;
	}
	
	public function updateCMSFields(FieldSet $fields) {
		$fields->addFieldToTab('Root.Content.Cleaning', new CheckboxField('TidyHtml', _t('TidyContent.TIDY_HTML', 'Tidy HTML')));
		$fields->addFieldToTab('Root.Content.Cleaning', new CheckboxField('PurifyHtml', _t('TidyContent.PURIFY_HTML', 'Purify HTML')));
		$fields->addFieldToTab('Root.Content.Cleaning', new CheckboxField('CleanOnSave', _t('TidyContent.CLEAN_ON_SAVE', 'Clean on Save')));
	}
	
	public function onBeforeWrite() {

		if ($this->owner->CleanOnSave) {
			
			if ($this->owner->PurifyHtml) {
				if ($this->owner->isChanged('Content')) {
					$this->owner->Content = singleton('CleanContentService')->purify($this->owner->Content);
				}
			}

			if ($this->owner->TidyHtml) {
				
				if ($this->owner->isChanged('Content')) {
					$this->owner->Content = singleton('CleanContentService')->tidy($this->owner->Content);
					
				}
			}
			
		}
	}

	/**
	 * Usable from templates if CleanOnSave isn't applicable
	 */
	public function Clean($field = 'Content') {
		$content = $this->owner->$field;
		if ($this->owner->PurifyHtml) {
			if ($this->owner->isChanged('Content')) {
				$content = singleton('CleanContentService')->purify($content);
			}
		}

		if ($this->owner->TidyHtml) {
			if ($this->owner->isChanged('Content')) {
				$content = singleton('CleanContentService')->tidy($content);
			}
		}
		return DBField::create('HTMLVarchar', $content);
	}
}
