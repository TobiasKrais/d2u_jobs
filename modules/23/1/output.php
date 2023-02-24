<?php
if (!function_exists('prepareText')) {
    /**
     * Replaces common text changes.
     * @param string $text Text in need of replacements
     * @return string Replaced text
     */
    function prepareText($text)
    {
        return str_replace('</li>', '</span></li>', str_replace('<li>', '<li><span>', str_replace('<ul>', '<ul class="bullets">', d2u_addon_frontend_helper::prepareEditorField($text))));
    }
}

$url_namespace = d2u_addon_frontend_helper::getUrlNamespace();
$url_id = d2u_addon_frontend_helper::getUrlId();

$category_id = 'REX_VALUE[1]';
$category = false;
if ($category_id > 0) {
    $category = new D2U_Jobs\Category($category_id, rex_clang::getCurrentId());
} else {
    if (filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'job_category_id' === $url_namespace) {
        $category_id = filter_input(INPUT_GET, 'job_category_id', FILTER_VALIDATE_INT);
        if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
            $category_id = $url_id;
        }
        $category = new D2U_Jobs\Category($category_id, rex_clang::getCurrentId());
    }
}

$hide_application_hint = 'REX_VALUE[2]' == 'true' ? true : false;
$show_json_ld = 'REX_VALUE[3]' == 'true' ? true : false;
$show_application_form = 'REX_VALUE[4]' == 'true' ? true : false;

if (rex::isBackend()) {
    // Ausgabe im BACKEND
?>
	<h1 style="font-size: 1.5em;">Stellenmarkt Ausgabe</h1>
<?php
    if (false === $category) {
        echo '<p>Anzuzeigende Kategorien: Alle</p>';
    } else {
        echo '<p>Anzuzeigende Kategorie: '. $category->name .'</p>';
    }
    if ($show_json_ld) {
        echo '<p>Die Anzeigen werden im JSON-LD Format für z.B. Google Jobs veröffentlicht.</p>';
    } else {
        echo '<p>Die Anzeigen werden NICHT im JSON-LD Format für z.B. Google Jobs veröffentlicht.</p>';
    }
} else {
    // FRONTEND Output
    $sprog = rex_addon::get('sprog');
    $tag_open = $sprog->getConfig('wildcard_open_tag');
    $tag_close = $sprog->getConfig('wildcard_close_tag');

    // Output job details
    if (filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT, ['options' => ['default' => 0]]) > 0 || 'job_id' === $url_namespace) {
        $job_id = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
        if (\rex_addon::get('url')->isAvailable() && $url_id > 0) {
            $job_id = $url_id;
        }

        $job = new D2U_Jobs\Job($job_id, rex_clang::getCurrentId());
        // Redirect if object is not online
        if ('online' != $job->online_status) {
            rex_redirect(rex_article::getNotfoundArticleId(), rex_clang::getCurrentId());
        }
        echo '<div class="col-12 col-md-8">';
        echo '<article class="job-box">';
        echo '<img src="'. ('' != $job->picture ? 'index.php?rex_media_type=d2u_jobs_jobheader&rex_media_file='. $job->picture : \rex_url::addonAssets('d2u_jobs', 'noavatar.jpg'))  .'" alt="'. strip_tags($job->name) .'">';
        if (!$show_application_form && $job->prolog) {
            echo '<div class="prolog">'. $job->prolog .'</div>';
        }
        echo '<div class="heading">';
        echo '<h2>'. $job->name .'</h2>';
        if ('' != $job->city || '' != $job->reference_number) {
            echo '<p><b>';
            if ('' != $job->city) {
                echo $tag_open .'d2u_jobs_region'. $tag_close .': '. $job->city . ('' != $job->reference_number ? ' / ' : '');
            }
            if ('' != $job->reference_number) {
                echo $tag_open .'d2u_jobs_reference_number'. $tag_close .': '. $job->reference_number;
            }
            echo '</b></p>';
        }
        echo '</div>';

        $application_form = rex_request('apply', 'int', 0) > 0 ? true : false;
        $job_application_link = $job->clang_id == rex_clang::getCurrentId() ? $job->getUrl() . (strstr($job->getUrl(), '?') ? '&' : '?').'apply=1' : rex_getUrl('', '', ['job_id' => $job->job_id, 'target_clang' => $job->clang_id, 'apply' => 1]);
        if ($application_form) {
            echo '<a name="application-form" class="anchor"></a>';
            echo '<h3>'. \Sprog\Wildcard::get('d2u_jobs_application_link', $job->clang_id) .'</h3>';
            $yform = new rex_yform();
            $form_data = 'hidden|job_name|'. $job->name . ($job->reference_number ? ' (Referenznummer: '. $job->reference_number .')' : '') .'|REQUEST
					hidden|job_clang_id|'. $job->clang_id .'|REQUEST
					text|name|'. \Sprog\Wildcard::get('d2u_helper_module_form_name', $job->clang_id) .' *|||{"required":"required"}
					text|address|'. \Sprog\Wildcard::get('d2u_helper_module_form_street', $job->clang_id) .'|||
					text|zip|'. \Sprog\Wildcard::get('d2u_helper_module_form_zip', $job->clang_id) .'|||
					text|city|'. \Sprog\Wildcard::get('d2u_helper_module_form_city', $job->clang_id) .'|||
					text|phone|'. \Sprog\Wildcard::get('d2u_helper_module_form_phone', $job->clang_id) .' *|||{"required":"required"}
					text|email|'. \Sprog\Wildcard::get('d2u_helper_module_form_email', $job->clang_id) .' *|||{"required":"required"}
					textarea|message|'. \Sprog\Wildcard::get('d2u_helper_module_form_message', $job->clang_id) .'
					upload|upload|'. \Sprog\Wildcard::get('d2u_jobs_module_attachment', $job->clang_id) .'|0,10000|.pdf,.odt,.doc,.docx,.zip
					checkbox|privacy_policy_accepted|'. \Sprog\Wildcard::get('d2u_helper_module_form_privacy_policy', $job->clang_id) .' *|0,1|0';
            if (rex_addon::get('yform_spam_protection')->isAvailable()) {
                $form_data .= '
					spam_protection|honeypot|Leave empty|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_spam_detected', $job->clang_id) .'|0';
            } else {
                $form_data .= '
					php|validate_timer|Spamprotection|<input name="validate_timer" type="hidden" value="'. microtime(true) .'" />|
					validate|customfunction|validate_timer|d2u_addon_frontend_helper::yform_validate_timer|10|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_spambots', $job->clang_id) .'|

					html|honeypot||<div class="mail-validate hide">
					text|mailvalidate|'. \Sprog\Wildcard::get('d2u_helper_module_form_email', $job->clang_id) .'||no_db
					validate|compare_value|mailvalidate||!=|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_spam_detected', $job->clang_id) .'|
					html|honeypot||</div>';
            }
            $form_data .= '
					html||<br>* '. \Sprog\Wildcard::get('d2u_helper_module_form_required', $job->clang_id) .'<br><br>

					submit|submit|'. \Sprog\Wildcard::get('d2u_helper_module_form_send', $job->clang_id) .'|no_db

					validate|empty|name|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_name', $job->clang_id) .'
					validate|empty|phone|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_phone', $job->clang_id) .'
					validate|empty|email|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_email', $job->clang_id) .'
					validate|type|email|email|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_email', $job->clang_id) .'
					validate|empty|privacy_policy_accepted|'. \Sprog\Wildcard::get('d2u_helper_module_form_validate_privacy_policy', $job->clang_id) .'

					action|tpl2email|d2u_jobs_thanks_application|email
					action|tpl2email|d2u_jobs_application|'. rex_config::get('d2u_jobs', 'email');

            $yform->setFormData(trim($form_data));
            $yform->setValueField('php', ['php_attach', \Sprog\Wildcard::get('d2u_jobs_module_attachment', $job->clang_id), '<?php if (isset($this->params["value_pool"]["files"])) { $this->params["value_pool"]["email_attachments"] = $this->params["value_pool"]["files"]; } ?>']);
            $yform->setObjectparams('form_action', $job_application_link);
            $yform->setObjectparams('form_anchor', 'application-form');
            $yform->setObjectparams('Error-occured', \Sprog\Wildcard::get('d2u_helper_module_form_validate_title', $job->clang_id));
            $yform->setObjectparams('real_field_names', true);

            // action - showtext
            $yform->setActionField('showtext', [\Sprog\Wildcard::get('d2u_jobs_module_form_thanks', $job->clang_id),
                '<div class="rex-message"><div class="rex-info"><p>',
                '</p></div></div>',
                1]);

            echo $yform->getForm();
        } else {
            if ('' != $job->hr4you_lead_in) {
                echo '<br>';
                echo $job->hr4you_lead_in;
            }
            if ('' != $job->tasks_heading) {
                echo '<h3>'. $job->tasks_heading .'</h3>';
                echo prepareText($job->tasks_text);
            }
            if ('' != $job->profile_heading) {
                echo '<h3>'. $job->profile_heading .'</h3>';
                echo prepareText($job->profile_text);
            }
            if ('' != $job->offer_heading) {
                echo '<h3>'. $job->offer_heading .'</h3>';
                echo prepareText($job->offer_text);
            }
            if ('' != $job->hr4you_url_application_form) {
                echo '<br><br>';
                echo '<p class="appendix"><a target="_blank" href="'. $job->hr4you_url_application_form .'">'. $tag_open .'d2u_jobs_hr4you_application_link'. $tag_close .'</a></p>';
            } elseif ($show_application_form) {
                echo '<br><br>';
                echo '<p class="appendix"><a href="'. $job_application_link .'" title="'. \Sprog\Wildcard::get('d2u_jobs_application_link', $job->clang_id) .'">'. \Sprog\Wildcard::get('d2u_jobs_application_link', $job->clang_id) .'</a>'
                    .'</p>';
            } elseif (false === $hide_application_hint) {
                echo '<br><br>';
                echo '<p class="appendix">'. $tag_open .'d2u_jobs_footer'. $tag_close
                    .'<br><br><a href="mailto:'. rex_config::get('d2u_jobs', 'email') .'" title="'. rex_config::get('d2u_jobs', 'email') .'">'. rex_config::get('d2u_jobs', 'email') .'</a>'
                    .'</p>';
            }
        }
        echo '</article>';
        echo '</div>';
        echo '<div class="sp sections-less hide-for-medium-up"></div>';
        echo '<div class="col-12 col-md-4">';
        echo '<div class="job-box contact">';
        echo $tag_open .'d2u_jobs_questions'. $tag_close .'<br><br>';
        echo '<div class="row">';
        echo '<div class="col-12 col-sm-4 col-md-12 col-lg-4">';
        echo '<img src="'. ('' != $job->contact->picture ? 'index.php?rex_media_type=d2u_jobs_contact&rex_media_file='. $job->contact->picture : \rex_url::addonAssets('d2u_jobs', 'noavatar.jpg'))  .'" alt="'. $job->contact->name .'">';
        echo '</div>';
        echo '<div class="col-12 col-sm-8 col-md-12 col-lg-8">';
        echo '<h3 class="contact-heading">'. $job->contact->name .'</h3>';
        if ('' != $job->contact->phone) {
            echo $tag_open .'d2u_jobs_phone'. $tag_close .': '. $job->contact->phone .'<br>';
        }
        if ('' != $job->contact->email) {
            echo '<a href="mailto:'. $job->contact->email .'" title="'. rex_config::get('d2u_jobs', 'email') .'">'.$job->contact->email .'</a><br>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Show job as JSON-LD
        if ($show_json_ld) {
            echo $job->getJsonLdCode();
        }
    } else {
        // Output Job list
        $jobs = D2U_Jobs\Job::getAll(rex_clang::getCurrentId(), $category_id, true);
        echo '<div class="col-12">';
        echo '<div class="row" data-match-height>';
        if (count($jobs) > 0) {
            echo '<div class="col-12">';
            echo '<h1>'. $tag_open .'d2u_jobs_vacancies'. $tag_close .' ';
            if (false !== $category) {
                echo $category->name;
            }
            echo '</h1>';
            echo '</div>';
            foreach ($jobs as $job) {
                echo '<div class="col-12 col-md-6 col-lg-4">';
                echo '<a href="'. $job->getUrl() .'" class="job-box-list-link" title="'. strip_tags($job->name).'">';
                echo '<div class="job-box job-box-list" data-height-watch>';
                echo '<img src="'. ('' != $job->picture ? 'index.php?rex_media_type=d2u_jobs_joblist&rex_media_file='. $job->picture : \rex_url::addonAssets('d2u_jobs', 'noavatar.jpg'))  .'" alt="'. strip_tags($job->name) .'">';
                echo '<h2>'. $job->name .'</h2>';
                if ('' != $job->city || '' != $job->reference_number) {
                    echo '<p>';
                    if ('' != $job->city) {
                        echo $tag_open .'d2u_jobs_region'. $tag_close .': '. $job->city . ('' != $job->reference_number ? ' / ' : '');
                    }
                    if ('' != $job->reference_number) {
                        echo $tag_open .'d2u_jobs_reference_number'. $tag_close .': '. $job->reference_number;
                    }
                    echo '</p>';
                }
                echo '</div>';
                echo '</a>';
                echo '</div>';
            }
        }
        echo '</div>';
        echo '</div>';
    }
}
