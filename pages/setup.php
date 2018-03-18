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
<p>Sammelthread fürs Addon im <a href="https://redaxo.org/forum/viewtopic.php?f=43&t=21966" target="_blank">Redaxo Forum</a>.</p>
<p>Fehlermeldungen bitte im Git Projekt unter
	<a href="https://github.com/TobiasKrais/d2u_jobs/issues" target="_blank">https://github.com/TobiasKrais/d2u_jobs/issues</a> melden.</p>
<h2>Changelog</h2>
<p>1.0.3-DEV:</p>
<ul>
	<li>Lieblingseditor aus D2U Helper Addon nutzbar.</li>
	<li>Bugfix: Fehler wenn Einstellungen noch nicht vorgenommen wurden.</li>
	<li>Bugfix: Job ID in URL mit aufgenommen.</li>
	<li>Import Plugin: Bei Installation des Autoexportes künftig Ausführung im Frontend und Backend.</li>
	<li>Import Plugin: Fehler wegen namespace behoben.</li>
</ul>
<p>1.0.2:</p>
<ul>
	<li>Bugfix: URL Addon Daten korrigiert.</li>
	<li>Bugfix: HR4You Autoimport Bugfix, damit Import nicht abbricht.</li>
	<li>Bugfix: Löschen von Kontaktbildern war ohne Warnung möglich.</li>
	<li>Bugfix: HR4You Import hat Bilder von Kontakten gelöscht, obwohl sie noch gebraucht wurden.</li>
	<li>Englisches Backend hinzugefügt.</li>
	<li>Bugfix Job Seiten wenn HR4You Import Sprache nicht Standardsprache vom D2U Helper Addon ist.</li>
	<li>Ubersetzungshilfe integriert.</li>
	<li>Editierrechte für Übersetzer eingeschränkt.</li>
	<li>Anpassungen an URL Addon 1.0.1.</li>
</ul>
<p>1.0.1:</p>
<ul>
	<li>Bugfix: Alle Kategorien bei Jobs anzeigen.</li>
</ul>
<p>1.0.0:</p>
<ul>
	<li>Initiale Version.</li>
</ul>