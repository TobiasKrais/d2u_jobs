<?php
/*
 * Modules
 */
$d2u_module_manager = new D2UModuleManager(D2UJobsModules::getModules(), "modules/", "d2u_jobs");

// D2UModuleManager actions
$d2u_module_id = rex_request('d2u_module_id', 'string');
$paired_module = rex_request('pair_'. $d2u_module_id, 'int');
$function = rex_request('function', 'string');
if($d2u_module_id != "") {
	$d2u_module_manager->doActions($d2u_module_id, $function, $paired_module);
}

// D2UModuleManager show list
$d2u_module_manager->showManagerList();

/*
 * Templates
 */
?>
<h2>Beispielseiten</h2>
<ul>
	<li>D2U Stellenmarkt Addon: <a href="https://www.kaltenbach.com/de/" target="_blank">
		www.kaltenbach.com</a>.</li>
</ul>
<h2>Support</h2>
<p>Fehlermeldungen bitte im Git Projekt unter
	<a href="https://github.com/TobiasKrais/d2u_jobs/issues" target="_blank">https://github.com/TobiasKrais/d2u_jobs/issues</a> melden.</p>
<h2>Changelog</h2>
<p>1.0.1:</p>
<ul>
	<li>Bugfix: Alle Kategorien bei Jobs anzeigen.</li>
</ul>
<p>1.0.0:</p>
<ul>
	<li>Initiale Version.</li>
</ul>