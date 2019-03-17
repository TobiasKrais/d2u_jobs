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

// Import from Redaxo 4 D2U Stellenmarkt
$sql = rex_sql::factory();
$sql->setQuery("SHOW TABLES LIKE '". rex::getTablePrefix() ."d2u_stellenmarkt_stellen'");
$old_tables_available = $sql->getRows() > 0 ? TRUE : FALSE;
if(rex_request('import', 'string') == "d2u_stellenmarkt" && $old_tables_available) {
	$sql->setQuery("DROP TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs`;		
			DROP TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang`;		
			DROP TABLE `". rex::getTablePrefix() ."d2u_jobs_categories`;		
			DROP TABLE `". rex::getTablePrefix() ."d2u_jobs_categories_lang`;		
			DROP TABLE `". rex::getTablePrefix() ."d2u_jobs_contacts`;");
	$sql->setQuery("RENAME TABLE `". rex::getTablePrefix() ."d2u_stellenmarkt_stellen` TO `". rex::getTablePrefix() ."d2u_jobs_jobs`;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs` CHANGE `stellen_id` `job_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs` CHANGE `interne_nummer` `reference_number` INT(10) NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs` CHANGE `kategorie_ids` `category_ids` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs` CHANGE `datum` `date` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs` CHANGE `ort` `city` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs` CHANGE `bild` `picture` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs` CHANGE `status` `online_status` VARCHAR(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'online';
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs` CHANGE `kontakt_id` `contact_id` INT(10) NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs`
			DROP `interne_bezeichnung`,
			DROP `artikel_id`;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs` ENGINE = InnoDB;
	
		RENAME TABLE `". rex::getTablePrefix() ."d2u_stellenmarkt_stellen_lang` TO `". rex::getTablePrefix() ."d2u_jobs_jobs_lang`;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` CHANGE `stellen_id` `job_id` INT(11) UNSIGNED NOT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` DROP PRIMARY KEY;
		UPDATE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` SET `clang_id` = (`clang_id` + 1);
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` ADD PRIMARY KEY (`job_id`,`clang_id`);
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` CHANGE `bezeichnung` `name` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` CHANGE `aufgaben_ueberschrift` `tasks_heading` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` CHANGE `aufgaben_text` `tasks_text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` CHANGE `profil_ueberschrift` `profile_heading` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` CHANGE `profil_text` `profile_text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` CHANGE `angebot_ueberschrift` `offer_heading` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` CHANGE `angebot_text` `offer_text` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` ADD `translation_needs_update` VARCHAR(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `offer_text`;
		UPDATE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` SET `translation_needs_update` = 'no';
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` ADD `updatedate` INT(11) NULL DEFAULT NULL;
		UPDATE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` SET `updatedate` = UNIX_TIMESTAMP();
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` ADD `updateuser` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` ENGINE = InnoDB;

		RENAME TABLE `". rex::getTablePrefix() ."d2u_stellenmarkt_kategorien` TO `". rex::getTablePrefix() ."d2u_jobs_categories`;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_categories` CHANGE `kategorie_id` `category_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_categories` ADD `priority` INT(10) NULL DEFAULT NULL AFTER `category_id`;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_categories` ADD `picture` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `priority`;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_categories` DROP `interne_bezeichnung`;
		UPDATE `". rex::getTablePrefix() ."d2u_jobs_categories` SET `priority` = 1;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_categories` ENGINE = InnoDB;

		RENAME TABLE `". rex::getTablePrefix() ."d2u_stellenmarkt_kategorien_lang` TO `". rex::getTablePrefix() ."d2u_jobs_categories_lang`;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_categories_lang` CHANGE `kategorie_id` `category_id` INT(11) UNSIGNED NOT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_categories_lang` DROP PRIMARY KEY;
		UPDATE `". rex::getTablePrefix() ."d2u_jobs_categories_lang` SET `clang_id` = (`clang_id` + 1);
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_categories_lang` ADD PRIMARY KEY (`category_id`,`clang_id`);
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_categories_lang` ADD `translation_needs_update` VARCHAR(7) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `name`;
		UPDATE `". rex::getTablePrefix() ."d2u_jobs_categories_lang` SET `translation_needs_update` = 'no';
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_categories_lang` ENGINE = InnoDB;

		RENAME TABLE `". rex::getTablePrefix() ."d2u_stellenmarkt_kontakt` TO `". rex::getTablePrefix() ."d2u_jobs_contacts`;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_contacts` CHANGE `kontakt_id` `contact_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_contacts` CHANGE `bild` `picture` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_contacts` CHANGE `telefon` `phone` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_contacts` ENGINE = InnoDB;");

	$error = $sql->hasError() ? $sql->getError() : "";
	
	$sql->setQuery("DROP FUNCTION IF EXISTS `getPriority`;");
	$sql->setQuery("delimiter $$
		CREATE FUNCTION `getPriority`() RETURNS int(11) DETERMINISTIC
		begin
			return if(@jobCatPrio, @jobCatPrio:=@jobCatPrio+1, @jobCatPrio:=1);
		end$$
		delimiter ;");
	$sql->setQuery("UPDATE ". rex::getTablePrefix() ."d2u_jobs_categories SET priority = getPriority() ORDER BY `priority`, `category_id`;
		DROP FUNCTION IF EXISTS `getPriority`;");

	$sql->setQuery("SHOW COLUMNS FROM ". \rex::getTablePrefix() ."d2u_jobs_jobs LIKE 'hr4you_jobid';");
	if($sql->getRows() > 0 && $error == "") {
		$sql->setQuery("ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs` CHANGE `hr4you_jobid` `hr4you_job_id` INT(10) NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs` ADD `hr4you_url_application_form` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL AFTER `hr4you_job_id`;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_jobs_lang` CHANGE `hr4you_einleitung` `hr4you_lead_in` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL;
		ALTER TABLE `". rex::getTablePrefix() ."d2u_jobs_categories` CHANGE `hr4you_berufskategorie_id` `hr4you_category_id` INT(10) NULL DEFAULT NULL;");
		$error = $sql->hasError() ? $sql->getError() : "";
	}

	if($error != "") {
		print rex_view::error('Fehler beim Import: '. $error);
	}
	else {
		print rex_view::success('Daten aus Redaxo 4 D2U Stellenmarkt Addon importiert und alte Tabellen gelöscht');
	}
}
else if($old_tables_available) {
	print "<fieldset style='background-color: white; padding: 1em; border: 1px solid #dfe3e9;'>";
	print "<h2>Import aus Redaxo 4 D2U Stellenmarkt Addon</h2>";
	print "<p>Es wurden die D2U Stellenmarkt Addon Tabellen aus Redaxo 4 in der Datenbank gefunden."
	. "Sollen die Daten importiert werden und die alten Tabellen gelöscht werden? ACHTUNG: dabei werden alle vorhandenen Daten gelöscht!</p>";
	print '<a href="'. rex_url::currentBackendPage(["import" => "d2u_stellenmarkt"], FALSE) .'"><button class="btn btn-save">Import und vorhandene Daten löschen</button></a>';
	print "</fieldset>";
}

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
<p>1.0.8-DEV:</p>
<ul>
	<li>YRewrite Multidomain support.</li>
	<li>Erlaubt HTML Tags im Namen der Stellenanzeige.</li>
</ul>
<p>1.0.7:</p>
<ul>
	<li>Sprachdetails werden ausgeblendet, wenn Speicherung der Sprache nicht vorgesehen ist.</li>
	<li>Bugfix: Prioritäten der Kategorienwurden beim Löschen eines Datensatzes nicht reorganisiert.</li>
	<li>Modul 23-1 "D2U Stellenmarkt - Stellenanzeigen" kann optional den allgemeinen Bewerbungshinweis ausblenden.</li>
</ul>
<p>1.0.6:</p>
<ul>
	<li>Bugfix: Deaktiviertes Addon zu deinstallieren führte zu fatal error.</li>
	<li>In den Einstellungen gibt es jetzt eine Option, eigene Übersetzungen in SProg dauerhaft zu erhalten.</li>
	<li>Niederländische Frontendübersetzung hinzugefügt.</li>
	<li>Bugfix: CronJob wird - wenn installiert - nicht immer richtig aktiviert.</li>
	<li>Referenznummer kein Pflichtfeld mehr.</li>
	<li>HR4You Import verwendet jetzt Redaxo eigene Methode zum einfügen von Bildern in den Medienpool (https://github.com/redaxo/redaxo/issues/1614).</li>
	<li>Media Manager Bildtypen für Module hinzugefügt.</li>
	<li>CSS für Module hinzugefügts.</li>
</ul>
<p>1.0.5:</p>
<ul>
	<li>Methode zum Erstellen von Meta Tags d2u_jobs_frontend_helper::getAlternateURLs() hinzugefügt.</li>
	<li>Methode zum Erstellen von Meta Tags d2u_jobs_frontend_helper::getMetaTags() hinzugefügt.</li>
	<li>Bugfix: Update über Installer endete im "Whoops..."</li>
	<li>Bugfix: Deinstallation des hr4you_import Plugins fehlerhaft.</li>
</ul>
<p>1.0.4:</p>
<ul>
	<li>HR4You Job ID in Übersichtsliste eingefügt.</li>
	<li>HR4You Import Logfehler korrigiert.</li>
	<li>Datenübernahme aus Redaxo 4 D2U Stellenmarkt Addon möglich. Dazu müssen lediglich die entsprechenden Tabellen des alten Addons in der Datenbank vorhanden sein.</li>
	<li>Bugfix: Löschen von Sprachen schlug fehl.</li>
	<li>Bugfix: Fehler beim Speichern von Namen mit einfachem Anführungszeichen behoben.</li>
</ul>
<p>1.0.3:</p>
<ul>
	<li>Standardbild aus Klasse in Modul verlegt.</li>
	<li>Bugfix beim Speichern der Sprache einer Stelle.</li>
	<li>YRewrite Multidomain Anpassungen.</li>
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