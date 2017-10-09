<?php
// save settings
if (filter_input(INPUT_POST, "btn_save") == 'save') {
	$settings = (array) rex_post('settings', 'array', []);

	// Linkmap Link and media needs special treatment
	$link_ids = filter_input_array(INPUT_POST, array('REX_INPUT_LINK'=> array('filter' => FILTER_VALIDATE_INT, 'flags' => FILTER_REQUIRE_ARRAY)));
	$settings['article_id'] = $link_ids["REX_INPUT_LINK"][1];
	
	// Save settings
	if(rex_config::set("d2u_jobs", $settings)) {
		echo rex_view::success(rex_i18n::msg('form_saved'));

		// Update url schemes
		if(rex_addon::get('url')->isAvailable()) {
			d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() ."d2u_jobs_url_jobs", $settings['article_id']);
			d2u_addon_backend_helper::update_url_scheme(rex::getTablePrefix() ."d2u_jobs_url_jobs_categories", $settings['article_id']);
			UrlGenerator::generatePathFile([]);
		}

		// Install / update language replacements
		d2u_jobs_lang_helper::factory()->install();
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
						d2u_addon_backend_helper::form_input('d2u_jobs_settings_email', 'settings[email]', $this->getConfig('email'), TRUE, FALSE, 'email');
						d2u_addon_backend_helper::form_linkfield('d2u_helper_article_id', '1', $this->getConfig('article_id'), rex_config::get("d2u_helper", "default_lang"));
					?>
				</div>
			</fieldset>
			<fieldset>
				<legend><small><i class="rex-icon rex-icon-language"></i></small> <?php echo rex_i18n::msg('d2u_helper_lang_replacements'); ?></legend>
				<div class="panel-body-wrapper slide">
					<?php
						foreach(rex_clang::getAll() as $rex_clang) {
							print '<dl class="rex-form-group form-group">';
							print '<dt><label>'. $rex_clang->getName() .'</label></dt>';
							print '<dd>';
							print '<select class="form-control" name="settings[lang_replacement_'. $rex_clang->getId() .']">';
							$replacement_options = [
								'd2u_helper_lang_english' => 'english',
								'd2u_helper_lang_german' => 'german',
								'd2u_helper_lang_french' => 'french',
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