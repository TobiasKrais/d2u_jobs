<?php
/**
 * Offers helper functions for language issues
 */
class d2u_jobs_lang_helper {
	/**
	 * @var string[] Array with english replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_english = [
		'd2u_jobs_announcement' => 'Complete announcement',
		'd2u_jobs_footer' => 'Please send us your complete application documents with the possible starting date and your salary expectations via email to:',
		'd2u_jobs_phone' => 'Phone',
		'd2u_jobs_questions' => 'If you have questions regarding this position, please contact:',
		'd2u_jobs_reference_number' => 'Reference number',
		'd2u_jobs_region' => 'Region',
		'd2u_jobs_vacancies' => 'Vacancies',
	];
	
	/**
	 * @var string[] Array with german replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_german = [
		'd2u_jobs_announcement' => 'Zur Ausschreibung',
		'd2u_jobs_footer' => 'Bitte senden Sie uns Ihre kompletten Bewerbungsunterlagen mit dem frühestmöglichen Eintrittstermin und Ihrer Gehaltvorstellung per E-Mail mit dem Stichwort „Karriere“ im Betreff an:',
		'd2u_jobs_phone' => 'Tel.',
		'd2u_jobs_questions' => 'Wenn Sie Fragen zur ausgeschriebenen Stelle haben, wenden Sie sich bitte an:',
		'd2u_jobs_reference_number' => 'Referenznummer',
		'd2u_jobs_region' => 'Region',
		'd2u_jobs_vacancies' => 'Stellenangebote',

	];
	
	/**
	 * @var string[] Array with french replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_french = [
		'd2u_jobs_announcement' => 'annonce complète',
		'd2u_jobs_footer' => 'Veuillez nous envoyer vos documents de demande complets avec la date de début possible et vos attentes de salaire par courrier électronique à:',
		'd2u_jobs_phone' => 'Tél.',
		'd2u_jobs_questions' => 'Si vous avez des questions concernant ce poste, veuillez contacter:',
		'd2u_jobs_reference_number' => 'Numéro de réference',
		'd2u_jobs_region' => 'Région',
		'd2u_jobs_vacancies' => "Offres d'emploi",
	];

	/**
	 * @var string[] Array with russian replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_russian = [
		'd2u_jobs_announcement' => 'Завершить объявление',
		'd2u_jobs_footer' => 'Отправьте нам свои полные документы с возможной начальной датой и ваши ожидания по зарплате по электронной почте:',
		'd2u_jobs_phone' => 'тел.',
		'd2u_jobs_questions' => 'Если у вас есть вопросы относительно этой позиции, пожалуйста, обращайтесь:',
		'd2u_jobs_reference_number' => 'ссылка',
		'd2u_jobs_region' => 'область',
		'd2u_jobs_vacancies' => 'вакансии',
	];

	/**
	 * @var string[] Array with chinese replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_chinese = [
		'd2u_jobs_announcement' => '招标',
		'd2u_jobs_footer' => '请将您的完整求职资料与可行的入职日期和薪水要求一并发送至电子邮箱：',
		'd2u_jobs_phone' => '电话',
		'd2u_jobs_questions' => '如果您对该职位有任何疑问，请联系我们：',
		'd2u_jobs_reference_number' => '参考号',
		'd2u_jobs_region' => '区域',
		'd2u_jobs_vacancies' => '招聘岗位',
	];
	
	/**
	 * Factory method.
	 * @return d2u_jobs_lang_helper Object
	 */
	public static function factory() {
		return new d2u_jobs_lang_helper();
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