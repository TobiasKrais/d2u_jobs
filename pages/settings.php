<?php
// save settings
if (filter_input(INPUT_POST, "btn_save") == 'save') {
	$settings = (array) rex_post('settings', 'array', []);

	// Linkmap Link and media needs special treatment
	$link_ids = filter_input_array(INPUT_POST, ['REX_INPUT_LINK'=> ['filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY]]);
	$settings['article_id'] = $link_ids["REX_INPUT_LINK"][1];
	
	// Special treatment for media fields
	$input_media = rex_post('REX_INPUT_MEDIA', 'array', []);
	$settings['logo'] = $input_media['logo'];

	// Checkbox also needs special treatment if empty
	$settings['hr4you_autoimport'] = array_key_exists('hr4you_autoimport', $settings) ? "active" : "inactive";
	$settings['lang_wildcard_overwrite'] = array_key_exists('lang_wildcard_overwrite', $settings) ? "true" : "false";
	
	// Save settings
	if(rex_config::set("d2u_jobs", $settings)) {
		echo rex_view::success(rex_i18n::msg('form_saved'));

		// Update url schemes
		if(\rex_addon::get('url')->isAvailable()) {
			d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() ."d2u_jobs_url_jobs", $settings['article_id']);
			d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() ."d2u_jobs_url_jobs_categories", $settings['article_id']);
		}

		// Install / update language replacements
		d2u_jobs_lang_helper::factory()->install();

		// Install / remove Cronjob
 		if(rex_plugin::get('d2u_jobs', 'hr4you_import')->isAvailable()) {
			$import_cronjob = d2u_jobs_import_conjob::factory();
			if($this->getConfig('hr4you_autoimport') == 'active') {
				if(!$import_cronjob->isInstalled()) {
					$import_cronjob->install();
				}
			}
			else {
				$import_cronjob->delete();
			}
		}
	}
	else {
		echo rex_view::error(rex_i18n::msg('form_save_error'));
	}
}
?>
<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
	<div class="panel panel-edit">
		<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_helper_settings'); ?></div></header>
		<div class="panel-body">
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-database"></i></small> <?php echo rex_i18n::msg('d2u_helper_settings'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						d2u_addon_backend_helper::form_input('d2u_jobs_settings_email', 'settings[email]', $this->getConfig('email'), true, false, 'email');
						d2u_addon_backend_helper::form_linkfield('d2u_helper_article_id', '1', $this->getConfig('article_id'), intval(rex_config::get("d2u_helper", "default_lang")));
					?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-language"></i></small> <?php echo rex_i18n::msg('d2u_helper_lang_replacements'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						d2u_addon_backend_helper::form_checkbox('d2u_helper_lang_wildcard_overwrite', 'settings[lang_wildcard_overwrite]', 'true', $this->getConfig('lang_wildcard_overwrite') == 'true');
						foreach(rex_clang::getAll() as $rex_clang) {
							print '<dl class="rex-form-group form-group">';
							print '<dt><label>'. $rex_clang->getName() .'</label></dt>';
							print '<dd>';
							print '<select class="form-control" name="settings[lang_replacement_'. $rex_clang->getId() .']">';
							$replacement_options = [
								'd2u_helper_lang_english' => 'english',
								'd2u_helper_lang_german' => 'german',
								'd2u_helper_lang_french' => 'french',
								'd2u_helper_lang_dutch' => 'dutch',
								'd2u_helper_lang_spanish' => 'spanish',
								'd2u_helper_lang_russian' => 'russian',
								'd2u_helper_lang_chinese' => 'chinese',
							];
							foreach($replacement_options as $key => $value) {
								$selected = $value == $this->getConfig('lang_replacement_'. $rex_clang->getId()) ? ' selected="selected"' : '';
								print '<option value="'. $value .'"'. $selected .'>'. rex_i18n::msg('d2u_helper_lang_replacements_install') .' '. rex_i18n::msg($key) .'</option>';
							}
							print '</select>';
							print '</dl>';
						}
					?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon fa-google"></i></small> <?php echo rex_i18n::msg('d2u_jobs_settings_google'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						d2u_addon_backend_helper::form_input('d2u_jobs_settings_company_name', 'settings[company_name]', $this->getConfig('company_name'), true, false, "text");
						d2u_addon_backend_helper::form_mediafield('d2u_jobs_settings_logo', 'logo', $this->getConfig('logo'));
					?>
				</div>
			</fieldset>
			<?php
				if(rex_plugin::get('d2u_jobs', 'hr4you_import')->isAvailable()) {
			?>
				<fieldset>
					<legend><small><i class="rex-icon fa-cloud-download"></i></small> <?php echo rex_i18n::msg('d2u_jobs_hr4you'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							// Default language for import
							if(count(rex_clang::getAll()) > 1) {
								$lang_options = [];
								foreach(rex_clang::getAll() as $rex_clang) {
									$lang_options[$rex_clang->getId()] = $rex_clang->getName();
								}
								d2u_addon_backend_helper::form_select('d2u_jobs_hr4you_settings_default_lang', 'settings[hr4you_default_lang]', $lang_options, [$this->getConfig('hr4you_default_lang')]);
							}
							d2u_addon_backend_helper::form_input('d2u_jobs_hr4you_settings_hr4you_xml_url', 'settings[hr4you_xml_url]', $this->getConfig('hr4you_xml_url'), false, false);
						?>
						<dl class="rex-form-group form-group" id="settings[hr4you_media_category]">
							<dt><label><?php echo rex_i18n::msg('d2u_jobs_hr4you_settings_hr4you_media_category'); ?></label></dt>
							<dd>
								<?php
									$media_category = new rex_media_category_select(false);
									$media_category->addOption(rex_i18n::msg('pool_kats_no'), 0);
									$media_category->get();
									$media_category->setSelected($this->getConfig('hr4you_media_category'));
									$media_category->setName('settings[hr4you_media_category]');
									$media_category->setAttribute('class', 'form-control');
									$media_category->show();
								?>
							</dd>
						</dl>
						<?php
							$job_category_options = [];
							foreach(D2U_Jobs\Category::getAll(rex_config::get('d2u_helper', 'default_lang'), false) as $job_category) {
								$job_category_options[$job_category->category_id] = $job_category->name;
							}
							d2u_addon_backend_helper::form_select('d2u_jobs_hr4you_settings_hr4you_default_category', 'settings[hr4you_default_category]', $job_category_options, [$this->getConfig('hr4you_default_category')]);
							$job_headline_options = [];
							for($i = 1; $i <= 6; $i++) {
								$job_headline_options['h'. $i] = htmlspecialchars('Überschrift <h'. $i .'>');
							}
							d2u_addon_backend_helper::form_select('d2u_jobs_hr4you_settings_headline_tag', 'settings[hr4you_headline_tag]', $job_headline_options, [$this->getConfig('hr4you_headline_tag')]);
							d2u_addon_backend_helper::form_checkbox('d2u_jobs_hr4you_settings_hr4you_autoimport', 'settings[hr4you_autoimport]', 'active', $this->getConfig('hr4you_autoimport') == 'active');
						?>
					</div>
				</fieldset>
			<?php
				}
			?>
		</div>
		<footer class="panel-footer">
			<div class="rex-form-panel-footer">
				<div class="btn-toolbar">
					<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="save"><?php echo rex_i18n::msg('form_save'); ?></button>
				</div>
			</div>
		</footer>
	</div>
</form>
<?php
	print d2u_addon_backend_helper::getCSS();
	print d2u_addon_backend_helper::getJS();
	print d2u_addon_backend_helper::getJSOpenAll();