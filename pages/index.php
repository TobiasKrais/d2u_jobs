<?php

echo rex_view::title(rex_i18n::msg('d2u_jobs'));

?>
<h2>Support</h2>
<p>Fehlermeldungen bitte im Git Projekt unter
	<a href="https://github.com/TobiasKrais/d2u_jobs/issues" target="_blank">https://github.com/TobiasKrais/d2u_jobs/issues</a> melden.</p>
<h2>Changelog</h2>
<p>2.0.0:</p>
<ul>
	<li>Dieses Addon benötigt nun das "jobs" Addon von FriendsOfRedaxo. Die eigentliche Funktionalität ist ins "jobs" Addon umgezogen.
        Dieses Addon stellt nur noch Funktionen zur Verfügung um einen problemlosen Übergang zur Verfügung zu stellen. Dazu gehört folgendes:
        <ul>
            <li>Die Namen der Datenbanktabellen haben sich geändert. Das Prefix "d2u_" wurde entfernt. Es werden aber VIEWs zur Verfügung gestellt, die die alten Tabellennamen verfügbar machen.</li>
            <li>Stellt die alten Sprog Ersetzungen zur Verfügung. Aus den Übersetzungen wurden der Prefix "d2u_" entfernt. Bitte Modulcode vor der Deinstallation des Addons anpassen.</li>
            <li>Stellt die Media Mananager Profile zur Verfügung. Aus den Namen der Profile wurd der Prefix "d2u_" entfernt. Bitte Modulcode vor der Deinstallation des Addons anpassen.</li>
            <li>Stellt die alten Klassen zur Verfügung. Bitte Modulcode vor der Deinstallation des Addons anpassen:</li>
                <ul>
                    <li><code>D2U_Jobs\Category</code> wird zu <code>FriendsOfRedaxo\Jobs\Category</code>.</li>
                    <li><code>D2U_Jobs\Contact</code> wird zu <code>FriendsOfRedaxo\Jobs\Contact</code>.</li>
                    <li><code>D2U_Jobs\Job</code> wird zu <code>FriendsOfRedaxo\Jobs\Job</code>.</li>
                    <li><code>D2U_Jobs\hr4you</code> wird zu <code>FriendsOfRedaxo\Jobs\Hr4youImport</code>.</li>
                </ul>
                Folgende interne Klassen wurden wurden ebenfalls umbenannt. Hier gibt es keine Übergangszeit, da sie nicht öffentlich sind:
                <ul>
                    <li><code>d2u_jobs_lang_helper</code> wird zu <code>FriendsOfRedaxo\Jobs\LangHelper</code>.</li>
                    <li><code>d2u_jobs_frontend_helper</code> wird zu <code>FriendsOfRedaxo\Jobs\FrontendHelper</code>.</li>
                    <li><code>d2u_jobs_import_conjob</code> wird zu <code>FriendsOfRedaxo\Jobs\JobLang</code>.</li>
                    <li><code>JobsModules</code> wird zu <code>FriendsOfRedaxo\Jobs\Module</code>.</li>			
                </ul>
            </li>
            <li>Die Beispielmodule werden mit der Installation des "jobs" Addons angepasst.</li>
        </ul>
	</li>
	<li>Das HR4You Plugin geht in das Addon auf. Falls der HR4You Autoimport genutzt wird, bitte den Cronjob von Hand löschen und über die Einstellungen nochmals installieren.</li>
	<li>Ca. 300 rexstan Level 9 Anpassungen.</li>
	<li>Spaltenüberschriften auf Seiten sind sortierbar.</li>
	<li>Modul "23-1 D2U Stellenmarkt - Stellenanzeigen": Filterfunktionen hinzugefügt und Spamschutz / CSRF Fehler behoben.</li>
	<li>Modul "23-1 D2U Stellenmarkt - Stellenanzeigen": Prolog wurde nicht immer angezeigt.</li>
	<li>Kontakte und Modul "23-1 D2U Stellenmarkt - Stellenanzeigen": Feld für WhatsApp Videobewerbung hinzugefügt.</li>
	<li>Einstellungen und Modul "23-1 D2U Stellenmarkt - Stellenanzeigen": Linkfeld für FAQ Seite hinzugefügt.</li>
	<li>Bugfix: JSON Ausgabe bei Verwendung von ' verbessert.</li>
    <li>Import aus Redaxo 4 entfernt.</li>
</ul>
<p>1.2.6:</p>
<ul>
	<li>Modul "23-1 D2U Stellenmarkt - Stellenanzeigen": Bewerbungsformular mit Formularnamen versehen um bessere YForm Spamprotection Kompatibilität bei mehreren Formularen auf einer Seite herzustellen.</li>
</ul>
<p>1.2.5:</p>
<ul>
	<li>hr4you Plugin: Bugfix für Pfadrechte des Cacheordners.</li>
</ul>
<p>1.2.4:</p>
<ul>
	<li>PHP-CS-Fixer Code Verbesserungen.</li>
	<li>Bugfix: Fehler bei Installation der Sprachen in Sprog behoben.</li>
	<li>Bugfix Kontakt ID wurde in aktueller install.php nicht hinzugefügt.</li>
	<li>Verbesserte Fehlermeldung, wenn vergessen wurde Sprachbezogene Daten einzugeben.</li>
	<li>hr4you_import Plugin: Wandelt HTML-Entities beim Import in ihre entsprechenden Zeichen um.</li>
</ul>
<p>1.2.3:</p>
<ul>
	<li>.github Verzeichnis aus Installer Action ausgeschlossen.</li>
	<li>Bugfix hr4you Import: Bilder wurden erst beim 2. Import mit der Stellenanzeige verknüpft.</li>
	<li>Bugfix: HTML aus Meta Beschreibung entfernt.</li>
	<li>Bugfix: HTML Attribute aus LD+JSON Code größtenteils entfernt.</li>
</ul>
<p>1.2.2:</p>
<ul>
	<li>Anpassungen an Publish Github Release to Redaxo.</li>
	<li>Unterstützt nur noch URL Addon >= 2.0.</li>
	<li>Bugfix: Ausgabe Texte für Google Jobs.</li>
	<li>Bugfix: Warnung beim Speichern von Stellen behoben.</li>
	<li>Bugfix: Beim Löschen von Medien die vom Addon verlinkt werden wurde der Name der verlinkenden Quelle in der Warnmeldung nicht immer korrekt angegeben.</li>
	<li>hr4you_import Plugin: Anpassungen an Medienpool 2.11.</li>
	<li>Modul "23-1 D2U Stellenmarkt - Stellenanzeigen": Sprache in Mails richtet sich nun nach Sprache der Stellenanzeige und nicht nach Frontendsprache.</li>
</ul>
<p>1.2.1:</p>
<ul>
	<li>Methoden zum Auslesen der Ländercodes und zur Ausgabe der Jobs nach Ländern hinzugefügt.</li>
	<li>Mehrsprachige Systeme: Stelle muss nicht mehr in Hautsprache eingegeben werden.</li>
	<li>Methode d2u_jobs_frontend_helper::getMetaTags() entfernt, da das URL Addon eine bessere Funktion anbietet.
		Ebenso die Methoden getMetaAlternateHreflangTags(), getMetaDescriptionTag(), getCanonicalTag und getTitleTag() der Klassen, die diese Methoden angeboten hatten.</li>
	<li>Modul "23-1 D2U Stellenmarkt - Stellenanzeigen": Bewerbungsformular optional hinzugefügt und Region in Ort umbenannt.</li>
	<li>Neues Feld Prolog hinzugefügt.</li>
	<li>Prüft vor dem Löschen einer Datei im Medienpool, ob sie in den Einstellungen des Addons verwendet wird.</li>
	<li>Spanische Übersetzungen werden nun korrekt installiert.</li>
</ul>
<p>1.2.0:</p>
<ul>
	<li>Bugfix: Beim Speichern von Stellen trat ein Fehler auf wenn das URL Addon >2 eingesetzt wird.</li>
	<li>Weitere Felder für Stellen hinzugefügt: Postleitzahl, Land und Stellenart.</li>
	<li>Modul 23-1 "D2U Stellenmarkt - Stellenanzeigen" kann Stellen nun im JSON-LD Format ausgeben, damit z.B. Google Jobs die Anzeigen auslesen kann.
		Zur Aktivierung müssen die neuen Optionen in den Einstellungen ausgefüllt werden und die neue Option im Beispielmodul aktiviert werden.</li>
</ul>
<p>1.1.0:</p>
<ul>
	<li>Bugfix beim Speichern einer Stelle wenn HR4You Plugin aktiviert ist.</li>
	<li>Benötigt Redaxo >= 5.10, da die neue Klasse rex_version verwendet wird.</li>
	<li>Modul 23-1 "D2U Stellenmarkt - Stellenanzeigen" leitet Offlinestellen auf die Fehlerseite weiter.</li>
	<li>Aktualisiert beim Speichern automatisch den search_it index.</li>
	<li>Spanische Frontend Übersetzungen hinzugefügt.</li>
	<li>Backend: Beim online stellen eines Stellenangebots in der Stellenliste gab es beim Aufruf im Frontend einen Fatal Error, da der URL cache nicht neu generiert wurde.</li>
	<li>Backend: Einstellungen und Setup Tabs rechts eingeordnet um sie vom Inhalt besser zu unterscheiden.</li>
	<li>Anpassungen an neueste Version des URL Addons Version 2.</li>
</ul>
<p>1.0.9:</p>
<ul>
	<li>Bugfix: Fatal error beim Speichern verursacht durch die URL Addon Version 2 Anpassungen behoben.</li>
</ul>
<p>1.0.8:</p>
<ul>
	<li>Bild in sitemap.xml eingefügt.</li>
	<li>Anpassungen an URL Addon 2.x.</li>
	<li>Listen im Backend werden jetzt nicht mehr in Seiten unterteilt.</li>
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
	<li>HR4You Import verwendet jetzt Redaxo eigene Methode zum Einfügen von Bildern in den Medienpool (https://github.com/redaxo/redaxo/issues/1614).</li>
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