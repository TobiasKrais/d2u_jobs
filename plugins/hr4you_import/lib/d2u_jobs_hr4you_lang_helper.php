<?php
/**
 * Offers helper functions for language issues
 */
class d2u_jobs_hr4you_lang_helper {
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
		$d2u_jobs = rex_addon::get('d2u_jobs');
		
		foreach($this->replacements_english as $key => $value) {
			$addWildcard = rex_sql::factory();

			foreach (rex_clang::getAllIds() as $clang_id) {
				// Load values for input
				if($d2u_jobs->hasConfig('lang_replacement_'. $clang_id) && $d2u_jobs->getConfig('lang_replacement_'. $clang_id) == 'chinese'
					&& isset($this->replacements_chinese) && isset($this->replacements_chinese[$key])) {
					$value = $this->replacements_chinese[$key];
				}
				else if($d2u_jobs->hasConfig('lang_replacement_'. $clang_id) && $d2u_jobs->getConfig('lang_replacement_'. $clang_id) == 'french'
					&& isset($this->replacements_french) && isset($this->replacements_french[$key])) {
					$value = $this->replacements_french[$key];
				}
				else if($d2u_jobs->hasConfig('lang_replacement_'. $clang_id) && $d2u_jobs->getConfig('lang_replacement_'. $clang_id) == 'german'
					&& isset($this->replacements_german) && isset($this->replacements_german[$key])) {
					$value = $this->replacements_german[$key];
				}
				else if($d2u_jobs->hasConfig('lang_replacement_'. $clang_id) && $d2u_jobs->getConfig('lang_replacement_'. $clang_id) == 'russian'
					&& isset($this->replacements_russian) && isset($this->replacements_russian[$key])) {
					$value = $this->replacements_russian[$key];
				}
				else { 
					$value = $this->replacements_english[$key];
				}

				if(\rex_addon::get('sprog')->isAvailable()) {
					$select_pid_query = "SELECT pid FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."' AND clang_id = ". $clang_id;
					$select_pid_sql = rex_sql::factory();
					$select_pid_sql->setQuery($select_pid_query);
					if($select_pid_sql->getRows() > 0) {
						// Update
						$query = "UPDATE ". rex::getTablePrefix() ."sprog_wildcard SET "
							."`replace` = '". addslashes($value) ."', "
							."updatedate = '". rex_sql::datetime() ."', "
							."updateuser = '". rex::getUser()->getValue('login') ."' "
							."WHERE pid = ". $select_pid_sql->getValue('pid');
						$sql = rex_sql::factory();
						$sql->setQuery($query);						
					}
					else {
						$id = 1;
						// Before inserting: id (not pid) must be same in all langs
						$select_id_query = "SELECT id FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."' AND id > 0";
						$select_id_sql = rex_sql::factory();
						$select_id_sql->setQuery($select_id_query);
						if($select_id_sql->getRows() > 0) {
							$id = $select_id_sql->getValue('id');
						}
						else {
							$select_id_query = "SELECT MAX(id) + 1 AS max_id FROM ". rex::getTablePrefix() ."sprog_wildcard";
							$select_id_sql = rex_sql::factory();
							$select_id_sql->setQuery($select_id_query);
							if($select_id_sql->getValue('max_id') != NULL) {
								$id = $select_id_sql->getValue('max_id');
							}
						}
						// Save
						$query = "INSERT INTO ". rex::getTablePrefix() ."sprog_wildcard SET "
							."id = ". $id .", "
							."clang_id = ". $clang_id .", "
							."wildcard = '". $key ."', "
							."`replace` = '". addslashes($value) ."', "
							."createdate = '". rex_sql::datetime() ."', "
							."createuser = '". rex::getUser()->getValue('login') ."', "
							."updatedate = '". rex_sql::datetime() ."', "
							."updateuser = '". rex::getUser()->getValue('login') ."'";
						$sql = rex_sql::factory();
						$sql->setQuery($query);
					}
				}
			}
		}
	}

	/**
	 * Uninstalls the replacement table for this addon.
	 * @param int $clang_id Redaxo language ID, if 0, replacements of all languages
	 * will be deleted. Otherwise only one specified language will be deleted.
	 */
	public function uninstall($clang_id = 0) {
		foreach($this->replacements_english as $key => $value) {
			if(\rex_addon::get('sprog')->isAvailable()) {
				// Delete 
				$query = "DELETE FROM ". rex::getTablePrefix() ."sprog_wildcard WHERE wildcard = '". $key ."'";
				if($clang_id > 0) {
					$query .= " AND clang_id = ". $clang_id;
				}
				$select = rex_sql::factory();
				$select->setQuery($query);
			}
		}
	}
}