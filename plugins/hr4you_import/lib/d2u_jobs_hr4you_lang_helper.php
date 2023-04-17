<?php
/**
 * @api
 * Offers helper functions for language issues.
 */
class d2u_jobs_hr4you_lang_helper extends \D2U_Helper\ALangHelper
{
    /**
     * @var array<string, string> Array with chinese replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_chinese = [
        'd2u_jobs_hr4you_offer_heading' => '我们的报价',
        'd2u_jobs_hr4you_profile_heading' => '您的个人资料',
        'd2u_jobs_hr4you_tasks_heading' => '你的任务',
    ];

    /**
     * @var array<string, string> Array with english replacements. Key is the wildcard,
     * value the replacement.
     */
    public $replacements_english = [
        'd2u_jobs_hr4you_offer_heading' => 'We offer',
        'd2u_jobs_hr4you_profile_heading' => 'Your Profile',
        'd2u_jobs_hr4you_tasks_heading' => 'Your Tasks',
    ];

    /**
     * @var array<string, string> Array with french replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_french = [
        'd2u_jobs_hr4you_offer_heading' => 'Notre offre',
        'd2u_jobs_hr4you_profile_heading' => 'Votre profil',
        'd2u_jobs_hr4you_tasks_heading' => 'Vos tâches',
    ];

    /**
     * @var array<string, string> Array with spanish replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_german = [
        'd2u_jobs_hr4you_offer_heading' => 'Unser Angebot',
        'd2u_jobs_hr4you_profile_heading' => 'Ihr Profil',
        'd2u_jobs_hr4you_tasks_heading' => 'Ihre Aufgaben',
    ];

    /**
     * @var array<string, string> Array with spanish replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_spanish = [
        'd2u_jobs_hr4you_offer_heading' => 'Ofrecemos',
        'd2u_jobs_hr4you_profile_heading' => 'Su Perfil',
        'd2u_jobs_hr4you_tasks_heading' => 'Sus tareas',
    ];

    /**
     * @var array<string, string> Array with russian replacements. Key is the wildcard,
     * value the replacement.
     */
    protected array $replacements_russian = [
        'd2u_jobs_hr4you_offer_heading' => 'Наше предложение',
        'd2u_jobs_hr4you_profile_heading' => 'Ваш профиль',
        'd2u_jobs_hr4you_tasks_heading' => 'Желаемая должность',
    ];

    /**
     * Factory method.
     * @return d2u_jobs_hr4you_lang_helper Object
     */
    public static function factory()
    {
        return new self();
    }

    /**
     * Installs the replacement table for this addon.
     */
    public function install(): void
    {
        foreach ($this->replacements_english as $key => $value) {
            foreach (rex_clang::getAllIds() as $clang_id) {
                $lang_replacement = rex_config::get('d2u_jobs', 'lang_replacement_'. $clang_id, '');

                // Load values for input
                if ('chinese' === $lang_replacement && isset($this->replacements_chinese) && isset($this->replacements_chinese[$key])) {
                    $value = $this->replacements_chinese[$key];
                } elseif ('french' === $lang_replacement && isset($this->replacements_french) && isset($this->replacements_french[$key])) {
                    $value = $this->replacements_french[$key];
                } elseif ('german' === $lang_replacement && isset($this->replacements_german) && isset($this->replacements_german[$key])) {
                    $value = $this->replacements_german[$key];
                } elseif ('russian' === $lang_replacement && isset($this->replacements_russian) && isset($this->replacements_russian[$key])) {
                    $value = $this->replacements_russian[$key];
                } else {
                    $value = $this->replacements_english[$key];
                }

                $overwrite = 'true' === rex_config::get('d2u_jobs', 'lang_wildcard_overwrite', false) ? true : false;
                parent::saveValue($key, $value, $clang_id, $overwrite);
            }
        }
    }
}
