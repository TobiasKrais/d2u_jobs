<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print comments
if($message != "") {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (filter_input(INPUT_POST, "btn_save") == 1 || filter_input(INPUT_POST, "btn_apply") == 1) {
	$form = (array) rex_post('form', 'array', []);

	// Media fields and links need special treatment
	$input_media = (array) rex_post('REX_INPUT_MEDIA', 'array', array());

	$success = TRUE;
	$job = FALSE;
	$job_id = $form['job_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($job === FALSE) {
			$job = new D2U_Jobs\Job($job_id, $rex_clang->getId());
			$job->job_id = $job_id; // Ensure correct ID in case first language has no object
			$job->reference_number = $form['reference_number'];
			$category_ids = isset($form['category_ids']) ? $form['category_ids'] : [];
			$job->categories =  [];
			foreach ($category_ids as $category_id) {
				$job->categories[$category_id] = new D2U_Jobs\Category($category_id, $rex_clang->getId());
			}
			$job->date = $form['date'];
			$job->city = $form['city'];
			$job->picture = $input_media[1];
			$job->online_status = array_key_exists('online_status', $form) ? "online" : "offline";
			$job->contact = new D2U_Jobs\Contact($form['contact_id']);
			if(rex_plugin::get('d2u_jobs', 'hr4you_import')->isAvailable()) {
				$job->hr4you_lead_in = $form['hr4you_lead_in'];
				$job->hr4you_url_application_form = $form['hr4you_url_application_form'];
			}
		}
		else {
			$job->clang_id = $rex_clang->getId();
		}
		$job->name = $form['lang'][$rex_clang->getId()]['name'];
		$job->tasks_heading = $form['lang'][$rex_clang->getId()]['tasks_heading'];
		$job->tasks_text = $form['lang'][$rex_clang->getId()]['tasks_text'];
		$job->profile_heading = $form['lang'][$rex_clang->getId()]['profile_heading'];
		$job->profile_text = $form['lang'][$rex_clang->getId()]['profile_text'];
		$job->offer_heading = $form['lang'][$rex_clang->getId()]['offer_heading'];
		$job->offer_text = $form['lang'][$rex_clang->getId()]['offer_text'];
		$job->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];

		if($job->translation_needs_update == "delete") {
			$job->delete(FALSE);
		}
		else if($job->save() > 0){
			$success = FALSE;
		}
		else {
			// remember id, for each database lang object needs same id
			$job_id = $job->job_id;
		}
	}

	// message output
	$message = 'form_save_error';
	if($success) {
		$message = 'form_saved';
	}
	
	// Redirect to make reload and thus double save impossible
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $job !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$job->job_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$job_id = $entry_id;
	if($job_id == 0) {
		$form = (array) rex_post('form', 'array', []);
		$job_id = $form['job_id'];
	}
	$job = new D2U_Jobs\Job($job_id, rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId()));
	$job->job_id = $job_id; // Ensure correct ID in case language has no object
	$job->delete();
	
	$func = '';
}
// Change online status of category
else if($func == 'changestatus') {
	$job_id = $entry_id;
	$job = new D2U_Jobs\Job($job_id, rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId()));
	$job->job_id = $job_id; // Ensure correct ID in case language has no object
	$job->changeStatus();
	
	header("Location: ". rex_url::currentBackendPage());
	exit;
}

// Form
if ($func == 'edit' || $func == 'clone' || $func == 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_jobs'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[job_id]" value="<?php echo ($func == 'edit' ? $entry_id : 0); ?>">
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_helper_data_all_lang'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							// Do not use last object from translations, because you don't know if it exists in DB
							$job = new D2U_Jobs\Job($entry_id, rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId()));
							if($job->job_id == 0) {
								// This must be an imported job auf default lang is different from hr4you import lang
								$job = new D2U_Jobs\Job($entry_id, rex_config::get("d2u_jobs", "hr4you_default_lang", rex_clang::getStartId()));
							}

							$readonly = TRUE;
							if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_jobs[edit_data]')) {
								$readonly = FALSE;
							}
							
							d2u_addon_backend_helper::form_input('d2u_jobs_reference_number', 'form[reference_number]', $job->reference_number, FALSE, $readonly, 'text');
							$options_categories = [];
							foreach(D2U_Jobs\Category::getAll(rex_config::get('d2u_helper', 'default_lang', rex_clang::getStartId()), FALSE) as $category) {
								$options_categories[$category->category_id] = $category->name;
							}
							d2u_addon_backend_helper::form_select('d2u_helper_category', 'form[category_ids][]', $options_categories, (count($job->categories) > 0 ? array_keys($job->categories) : []), 5, TRUE, $readonly);
							d2u_addon_backend_helper::form_input('d2u_jobs_date', 'form[date]', $job->date, TRUE, $readonly, 'date');
							d2u_addon_backend_helper::form_input('d2u_jobs_city', 'form[city]', $job->city, TRUE, $readonly);
							d2u_addon_backend_helper::form_checkbox('d2u_helper_online_status', 'form[online_status]', 'online', $job->online_status == "online", $readonly);
							d2u_addon_backend_helper::form_mediafield('d2u_helper_picture', '1', $job->picture, $readonly);
							$options_contacts = [];
							foreach(D2U_Jobs\Contact::getAll() as $contact) {
								if($contact->name != "") {
									$options_contacts[$contact->contact_id] = $contact->name;
								}
							}
							d2u_addon_backend_helper::form_select('d2u_jobs_contact', 'form[contact_id]', $options_contacts, ($job->contact === FALSE ? [] : [$job->contact->contact_id]), 1, FALSE, $readonly);
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
								d2u_addon_backend_helper::form_input('d2u_jobs_hr4you_import_job_id', 'form[hr4you_job_id]', $job->hr4you_job_id, FALSE, TRUE, 'number');
								d2u_addon_backend_helper::form_input('d2u_jobs_hr4you_import_lead_in', 'form[hr4you_lead_in]', $job->hr4you_lead_in, FALSE, $readonly);
								d2u_addon_backend_helper::form_input('d2u_jobs_hr4you_url_application_form', 'form[hr4you_url_application_form]', $job->hr4you_url_application_form, FALSE, $readonly);
							?>
						</div>
					</fieldset>
				<?php
					}
					foreach(rex_clang::getAll() as $rex_clang) {
						$job = new D2U_Jobs\Job($entry_id, $rex_clang->getId());
						$required = $rex_clang->getId() == rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId()) ? TRUE : FALSE;
						
						$readonly_lang = TRUE;
						if(rex::getUser()->isAdmin() || (rex::getUser()->hasPerm('d2u_jobs[edit_lang]') && rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId()))) {
							$readonly_lang = FALSE;
						}
				?>
					<fieldset>
						<legend><?php echo rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"'; ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								if($rex_clang->getId() != rex_config::get("d2u_helper", "default_lang", rex_clang::getStartId())) {
									$options_translations = [];
									$options_translations["yes"] = rex_i18n::msg('d2u_helper_translation_needs_update');
									$options_translations["no"] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
									$options_translations["delete"] = rex_i18n::msg('d2u_helper_translation_delete');
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$job->translation_needs_update], 1, FALSE, $readonly_lang);
								}
								else {
									print '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
								}
								
								d2u_addon_backend_helper::form_input('d2u_helper_name', "form[lang][". $rex_clang->getId() ."][name]", $job->name, $required, $readonly_lang);
								d2u_addon_backend_helper::form_input('d2u_jobs_tasks_heading', "form[lang][". $rex_clang->getId() ."][tasks_heading]", $job->tasks_heading, $required, $readonly_lang);
								d2u_addon_backend_helper::form_textarea('d2u_jobs_tasks_text', "form[lang][". $rex_clang->getId() ."][tasks_text]", $job->tasks_text, 5, FALSE, $readonly_lang, TRUE);
								d2u_addon_backend_helper::form_input('d2u_jobs_profile_heading', "form[lang][". $rex_clang->getId() ."][profile_heading]", $job->profile_heading, FALSE, $readonly_lang);
								d2u_addon_backend_helper::form_textarea('d2u_jobs_profile_text', "form[lang][". $rex_clang->getId() ."][profile_text]", $job->profile_text, 5, FALSE, $readonly_lang, TRUE);
								d2u_addon_backend_helper::form_input('d2u_jobs_offer_heading', "form[lang][". $rex_clang->getId() ."][offer_heading]", $job->offer_heading, FALSE, $readonly_lang);
								d2u_addon_backend_helper::form_textarea('d2u_jobs_offer_text', "form[lang][". $rex_clang->getId() ."][offer_text]", $job->offer_text, 5, FALSE, $readonly_lang, TRUE);
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
						<button class="btn btn-save rex-form-aligned" type="submit" name="btn_save" value="1"><?php echo rex_i18n::msg('form_save'); ?></button>
						<button class="btn btn-apply" type="submit" name="btn_apply" value="1"><?php echo rex_i18n::msg('form_apply'); ?></button>
						<button class="btn btn-abort" type="submit" name="btn_abort" formnovalidate="formnovalidate" value="1"><?php echo rex_i18n::msg('form_abort'); ?></button>
						<?php
							if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_jobs[edit_data]')) {
								print '<button class="btn btn-delete" type="submit" name="btn_delete" formnovalidate="formnovalidate" data-confirm="'. rex_i18n::msg('form_delete') .'?" value="1">'. rex_i18n::msg('form_delete') .'</button>';
							}
						?>
					</div>
				</div>
			</footer>
		</div>
	</form>
	<br>
	<?php
		print d2u_addon_backend_helper::getCSS();
		print d2u_addon_backend_helper::getJS();
}

if ($func == '') {
	$query = 'SELECT job.job_id, name, `date`, city, online_status '. (rex_plugin::get('d2u_jobs', 'hr4you_import')->isAvailable() ? ', hr4you_job_id ' : '')
		. 'FROM '. rex::getTablePrefix() .'d2u_jobs_jobs AS job '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_jobs_lang AS lang '
			. 'ON job.job_id = lang.job_id AND lang.clang_id = '. (rex_config::get("d2u_jobs", "hr4you_default_lang", 0) > 0 ? rex_config::get("d2u_jobs", "hr4you_default_lang") : rex_config::get("d2u_helper", "default_lang")) .' '
		.'ORDER BY online_status DESC, name ASC';
    $list = rex_list::factory($query);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon fa-users"></i>';
 	$thIcon = "";
	if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_jobs[edit_data]')) {
		$thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
	}
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###job_id###']);

    $list->setColumnLabel('job_id', rex_i18n::msg('id'));
    $list->setColumnLayout('job_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###job_id###']);

    $list->setColumnLabel('date', rex_i18n::msg('d2u_jobs_date'));
    $list->setColumnParams('date', ['func' => 'edit', 'entry_id' => '###job_id###']);

    $list->setColumnLabel('city', rex_i18n::msg('d2u_jobs_city'));
    $list->setColumnParams('city', ['func' => 'edit', 'entry_id' => '###job_id###']);
	
	if(rex_plugin::get('d2u_jobs', 'hr4you_import')->isAvailable()) {
		$list->setColumnLabel('hr4you_job_id', rex_i18n::msg('d2u_jobs_hr4you_import_job_id'));
	}

	$list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###job_id###']);

	$list->removeColumn('online_status');
	if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_jobs[edit_data]')) {
		$list->addColumn(rex_i18n::msg('status_online'), '<a class="rex-###online_status###" href="' . rex_url::currentBackendPage(['func' => 'changestatus']) . '&entry_id=###job_id###"><i class="rex-icon rex-icon-###online_status###"></i> ###online_status###</a>');
		$list->setColumnLayout(rex_i18n::msg('status_online'), ['', '<td class="rex-table-action">###VALUE###</td>']);

		$list->addColumn(rex_i18n::msg('d2u_helper_clone'), '<i class="rex-icon fa-copy"></i> ' . rex_i18n::msg('d2u_helper_clone'));
		$list->setColumnLayout(rex_i18n::msg('d2u_helper_clone'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('d2u_helper_clone'), ['func' => 'clone', 'entry_id' => '###job_id###']);

		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###job_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

    $list->setNoRowsMessage(rex_i18n::msg('d2u_jobs_no_jobs_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_jobs_jobs'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}