<?php
/**
 * Offers helper functions for language issues
 */
class d2u_jobs_hr4you_lang_helper extends \D2U_Helper\ALangHelper {
	/**
	 * @var string[] Array with english replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = [
		'd2u_jobs_hr4you_application_link' => 'Online application form',
		'd2u_jobs_hr4you_offer_heading' => 'We offer',
		'd2u_jobs_hr4you_profile_heading' => 'Your Profile',
		'd2u_jobs_hr4you_tasks_heading' => 'Your Tasks',
	];
	
	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = [
		'd2u_jobs_hr4you_application_link' => 'Online Bewerbungsformular',
		'd2u_jobs_hr4you_offer_heading' => 'Unser Angebot',
		'd2u_jobs_hr4you_profile_heading' => 'Ihr Profil',
		'd2u_jobs_hr4you_tasks_heading' => 'Ihre Aufgaben',
	];
	
	/**
	 * @var string[] Array with french replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_french = [
		'd2u_jobs_hr4you_application_link' => 'Formulaire de demande en ligne',
		'd2u_jobs_hr4you_offer_heading' => 'Notre offre',
		'd2u_jobs_hr4you_profile_heading' => 'Votre profil',
		'd2u_jobs_hr4you_tasks_heading' => 'Vos tâches',
	];

	/**
	 * @var string[] Array with russian replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_russian = [
		'd2u_jobs_hr4you_application_link' => 'Онлайн-заявка',
		'd2u_jobs_hr4you_offer_heading' => 'наше предложение',
		'd2u_jobs_hr4you_profile_heading' => 'Ваш профиль',
		'd2u_jobs_hr4you_tasks_heading' => 'ваши задания',
	];

	/**
	 * @var string[] Array with chinese replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_chinese = [
		'd2u_jobs_hr4you_application_link' => '在线申请表',
		'd2u_jobs_hr4you_offer_heading' => '我们的报价',
		'd2u_jobs_hr4you_profile_heading' => '您的个人资料',
		'd2u_jobs_hr4you_tasks_heading' => '你的任务',
	];
	
	/**
	 * Factory method.
	 * @return d2u_immo_lang_helper Object
	 */
	public static function factory() {
		return new d2u_jobs_hr4you_lang_helper();
	}
	
	/**
	 * Installs the replacement table for this addon.
	 */
	public function install() {
		foreach($this->replacements_english as $key => $value) {
			foreach (rex_clang::getAllIds() as $clang_id) {
				$lang_replacement = rex_config::get('d2u_jobs', 'lang_replacement_'. $clang_id, '');

				// Load values for input
				if($lang_replacement === 'chinese' && isset($this->replacements_chinese) && isset($this->replacements_chinese[$key])) {
					$value = $this->replacements_chinese[$key];
				}
				else if($lang_replacement === 'french' && isset($this->replacements_french) && isset($this->replacements_french[$key])) {
					$value = $this->replacements_french[$key];
				}
				else if($lang_replacement === 'german' && isset($this->replacements_german) && isset($this->replacements_german[$key])) {
					$value = $this->replacements_german[$key];
				}
				else if($lang_replacement === 'russian' && isset($this->replacements_russian) && isset($this->replacements_russian[$key])) {
					$value = $this->replacements_russian[$key];
				}
				else { 
					$value = $this->replacements_english[$key];
				}

				$overwrite = rex_config::get('d2u_helper', 'lang_wildcard_overwrite', FALSE) === "true" ? TRUE : FALSE;
				parent::saveValue($key, $value, $clang_id, $overwrite);
			}
		}
	}
}