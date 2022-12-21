<?php
$func = rex_request('func', 'string');
$entry_id = rex_request('entry_id', 'int');
$message = rex_get('message', 'string');

// Print comments
if($message != "") {
	print rex_view::success(rex_i18n::msg($message));
}

// save settings
if (intval(filter_input(INPUT_POST, "btn_save")) === 1 || intval(filter_input(INPUT_POST, "btn_apply")) === 1) {
	$form = (array) rex_post('form', 'array', []);

	// Media fields and links need special treatment
	$input_media = (array) rex_post('REX_INPUT_MEDIA', 'array', array());

	$success = TRUE;
	$category = FALSE;
	$category_id = $form['category_id'];
	foreach(rex_clang::getAll() as $rex_clang) {
		if($category === FALSE) {
			$category = new D2U_Jobs\Category($category_id, $rex_clang->getId());
			$category->category_id = $category_id; // Ensure correct ID in case first language has no object
			$category->priority = $form['priority'];
			$category->picture = $input_media[1];
			if(rex_plugin::get('d2u_jobs', 'hr4you_import')->isAvailable()) {
				$category->hr4you_category_id = $form['hr4you_category_id'];
			}
		}
		else {
			$category->clang_id = $rex_clang->getId();
		}
		$category->name = $form['lang'][$rex_clang->getId()]['name'];
		$category->translation_needs_update = $form['lang'][$rex_clang->getId()]['translation_needs_update'];
		
		if($category->translation_needs_update == "delete") {
			$category->delete(FALSE);
		}
		else if($category->save() > 0){
			$success = FALSE;
		}
		else {
			// remember id, for each database lang object needs same id
			$category_id = $category->category_id;
		}
	}

	// message output
	$message = 'form_save_error';
	if($success) {
		$message = 'form_saved';
	}
	
	// Redirect to make reload and thus double save impossible
	if(filter_input(INPUT_POST, "btn_apply") == 1 && $category !== FALSE) {
		header("Location: ". rex_url::currentBackendPage(array("entry_id"=>$category->category_id, "func"=>'edit', "message"=>$message), FALSE));
	}
	else {
		header("Location: ". rex_url::currentBackendPage(array("message"=>$message), FALSE));
	}
	exit;
}
// Delete
else if(filter_input(INPUT_POST, "btn_delete") == 1 || $func == 'delete') {
	$category_id = $entry_id;
	if($category_id == 0) {
		$form = (array) rex_post('form', 'array', []);
		$category_id = $form['category_id'];
	}
	$category = new D2U_Jobs\Category($category_id, intval(rex_config::get("d2u_helper", "default_lang")));
	$category->category_id = $category_id; // Ensure correct ID in case language has no object

	// Check if object is used
	$uses_jobs = $category->getJobs();

	if(count($uses_jobs) == 0) {
		$category->delete(TRUE);
	}
	else {
		$message = '<ul>';
		foreach($uses_jobs as $uses_job) {
			$message .= '<li><a href="index.php?page=d2u_jobs/jobs&func=edit&entry_id='. $uses_job->job_id .'">'. $uses_job->name.'</a></li>';
		}
		$message .= '</ul>';

		print rex_view::error(rex_i18n::msg('d2u_helper_could_not_delete') . $message);
	}
	
	$func = '';
}
// Change online status of category
else if($func == 'changestatus') {
	$category_id = $entry_id;
	$category = new D2U_Jobs\Category($category_id, intval(rex_config::get("d2u_helper", "default_lang")));
	$category->category_id = $category_id; // Ensure correct ID in case language has no object
	$category->changeStatus();
	
	header("Location: ". rex_url::currentBackendPage());
	exit;
}

// Form
if ($func == 'edit' || $func == 'add') {
?>
	<form action="<?php print rex_url::currentBackendPage(); ?>" method="post">
		<div class="panel panel-edit">
			<header class="panel-heading"><div class="panel-title"><?php print rex_i18n::msg('d2u_jobs'); ?></div></header>
			<div class="panel-body">
				<input type="hidden" name="form[category_id]" value="<?php echo $entry_id; ?>">
				<fieldset>
					<legend><?php echo rex_i18n::msg('d2u_helper_data_all_lang'); ?></legend>
					<div class="panel-body-wrapper slide">
						<?php
							// Do not use last object from translations, because you don't know if it exists in DB
							$category = new D2U_Jobs\Category($entry_id, intval(rex_config::get("d2u_helper", "default_lang")));
							$readonly = TRUE;
							if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_jobs[edit_data]')) {
								$readonly = FALSE;
							}
							
							d2u_addon_backend_helper::form_input('header_priority', 'form[priority]', $category->priority, TRUE, $readonly, 'number');
							d2u_addon_backend_helper::form_mediafield('d2u_helper_picture', '1', $category->picture, $readonly);
							if(rex_plugin::get('d2u_jobs', 'hr4you_import')->isAvailable()) {
								d2u_addon_backend_helper::form_input('d2u_jobs_hr4you_category_id', 'form[hr4you_category_id]', $category->hr4you_category_id, FALSE, $readonly, 'number');
							}
						?>
					</div>
				</fieldset>
				<?php
					foreach(rex_clang::getAll() as $rex_clang) {
						$category = new D2U_Jobs\Category($entry_id, $rex_clang->getId());
						$required = $rex_clang->getId() === intval(rex_config::get("d2u_helper", "default_lang")) ? TRUE : FALSE;
						
						$readonly_lang = TRUE;
						if(rex::getUser()->isAdmin() || (rex::getUser()->hasPerm('d2u_jobs[edit_lang]') && rex::getUser()->getComplexPerm('clang')->hasPerm($rex_clang->getId()))) {
							$readonly_lang = FALSE;
						}
				?>
					<fieldset>
						<legend><?php echo rex_i18n::msg('d2u_helper_text_lang') .' "'. $rex_clang->getName() .'"'; ?></legend>
						<div class="panel-body-wrapper slide">
							<?php
								if($rex_clang->getId() !== intval(rex_config::get("d2u_helper", "default_lang"))) {
									$options_translations = [];
									$options_translations["yes"] = rex_i18n::msg('d2u_helper_translation_needs_update');
									$options_translations["no"] = rex_i18n::msg('d2u_helper_translation_is_uptodate');
									$options_translations["delete"] = rex_i18n::msg('d2u_helper_translation_delete');
									d2u_addon_backend_helper::form_select('d2u_helper_translation', 'form[lang]['. $rex_clang->getId() .'][translation_needs_update]', $options_translations, [$category->translation_needs_update], 1, FALSE, $readonly_lang);
								}
								else {
									print '<input type="hidden" name="form[lang]['. $rex_clang->getId() .'][translation_needs_update]" value="">';
								}
							?>
							<script>
								// Hide on document load
								$(document).ready(function() {
									toggleClangDetailsView(<?php print $rex_clang->getId(); ?>);
								});

								// Hide on selection change
								$("select[name='form[lang][<?php print $rex_clang->getId(); ?>][translation_needs_update]']").on('change', function(e) {
									toggleClangDetailsView(<?php print $rex_clang->getId(); ?>);
								});
							</script>
							<div id="details_clang_<?php print $rex_clang->getId(); ?>">
								<?php
									d2u_addon_backend_helper::form_input('d2u_helper_name', "form[lang][". $rex_clang->getId() ."][name]", $category->name, $required, $readonly_lang);
								?>
							</div>
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
	$query = 'SELECT category.category_id, name, priority '
		. 'FROM '. rex::getTablePrefix() .'d2u_jobs_categories AS category '
		. 'LEFT JOIN '. rex::getTablePrefix() .'d2u_jobs_categories_lang AS lang '
			. 'ON category.category_id = lang.category_id AND lang.clang_id = '. intval(rex_config::get("d2u_helper", "default_lang")) .' '
		.'ORDER BY priority ASC';
    $list = rex_list::factory($query, 1000);

    $list->addTableAttribute('class', 'table-striped table-hover');

    $tdIcon = '<i class="rex-icon rex-icon-open-category"></i>';
 	$thIcon = "";
	if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_jobs[edit_data]')) {
		$thIcon = '<a href="' . $list->getUrl(['func' => 'add']) . '" title="' . rex_i18n::msg('add') . '"><i class="rex-icon rex-icon-add-module"></i></a>';
	}
    $list->addColumn($thIcon, $tdIcon, 0, ['<th class="rex-table-icon">###VALUE###</th>', '<td class="rex-table-icon">###VALUE###</td>']);
    $list->setColumnParams($thIcon, ['func' => 'edit', 'entry_id' => '###category_id###']);

    $list->setColumnLabel('category_id', rex_i18n::msg('id'));
    $list->setColumnLayout('category_id', ['<th class="rex-table-id">###VALUE###</th>', '<td class="rex-table-id">###VALUE###</td>']);

    $list->setColumnLabel('name', rex_i18n::msg('d2u_helper_name'));
    $list->setColumnParams('name', ['func' => 'edit', 'entry_id' => '###category_id###']);

    $list->setColumnLabel('priority', rex_i18n::msg('header_priority'));

	$list->addColumn(rex_i18n::msg('module_functions'), '<i class="rex-icon rex-icon-edit"></i> ' . rex_i18n::msg('edit'));
    $list->setColumnLayout(rex_i18n::msg('module_functions'), ['<th class="rex-table-action" colspan="2">###VALUE###</th>', '<td class="rex-table-action">###VALUE###</td>']);
    $list->setColumnParams(rex_i18n::msg('module_functions'), ['func' => 'edit', 'entry_id' => '###category_id###']);

	if(rex::getUser()->isAdmin() || rex::getUser()->hasPerm('d2u_jobs[edit_data]')) {
		$list->addColumn(rex_i18n::msg('delete_module'), '<i class="rex-icon rex-icon-delete"></i> ' . rex_i18n::msg('delete'));
		$list->setColumnLayout(rex_i18n::msg('delete_module'), ['', '<td class="rex-table-action">###VALUE###</td>']);
		$list->setColumnParams(rex_i18n::msg('delete_module'), ['func' => 'delete', 'entry_id' => '###category_id###']);
		$list->addLinkAttribute(rex_i18n::msg('delete_module'), 'data-confirm', rex_i18n::msg('d2u_helper_confirm_delete'));
	}

    $list->setNoRowsMessage(rex_i18n::msg('d2u_helper_no_categories_found'));

    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('d2u_helper_category'), false);
    $fragment->setVar('content', $list->get(), false);
    echo $fragment->parse('core/page/section.php');
}