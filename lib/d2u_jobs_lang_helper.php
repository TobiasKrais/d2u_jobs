<?php
/**
 * Offers helper functions for language issues
 */
class d2u_jobs_lang_helper extends \D2U_Helper\ALangHelper {
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
	 * @var string[] Array with netherlands replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_dutch = [
		'd2u_jobs_announcement' => 'Naar openstaande vacatures',
		'd2u_jobs_footer' => 'U kunt uw motivatiebrief met CV onder vermelding van vroegst mogelijke opzegtermijn en salaris wens per E-Mail, met in het onderwerp „Sollicitatie <Vacature nummer>” sturen naar:',
		'd2u_jobs_phone' => 'Telefoon:',
		'd2u_jobs_questions' => 'Indien u naar aanleiding van deze vacature nog vragen heeft kunt u contact opnemen met:',
		'd2u_jobs_reference_number' => 'Vacature nummer',
		'd2u_jobs_region' => 'Regio',
		'd2u_jobs_vacancies' => "Vacatures",
	];

	/**
	 * @var string[] Array with russian replacements. Key is the wildcard,
	 * value the replacement. 
	 */
	protected $replacements_russian = [
		'd2u_jobs_announcement' => 'Отправить заявку',
		'd2u_jobs_footer' => 'Присылайте Ваше резюме и документы с возможной датой начала работы и ваши ожидания по зарплате по электронной почте:',
		'd2u_jobs_phone' => 'тел.',
		'd2u_jobs_questions' => 'Если у вас есть вопросы относительно этой вакансии, мы всегда к Вашим услугам:',
		'd2u_jobs_reference_number' => 'ссылка',
		'd2u_jobs_region' => 'Область',
		'd2u_jobs_vacancies' => 'Вакансии',
	];

	/**
	 * Factory method.
	 * @return d2u_jobs_lang_helper Object
	 */
	public static function factory() {
		return new self();
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
				else if($lang_replacement === 'dutch' && isset($this->replacements_dutch) && isset($this->replacements_dutch[$key])) {
					$value = $this->replacements_dutch[$key];
				}
				else if($lang_replacement === 'russian' && isset($this->replacements_russian) && isset($this->replacements_russian[$key])) {
					$value = $this->replacements_russian[$key];
				}
				else { 
					$value = $this->replacements_english[$key];
				}

				$overwrite = rex_config::get('d2u_jobs', 'lang_wildcard_overwrite', FALSE) === "true" ? TRUE : FALSE;
				parent::saveValue($key, $value, $clang_id, $overwrite);
			}
		}
	}
}