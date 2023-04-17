<h1>HR4YOU Import Plugin </h1>
<?php
$func = rex_request('func', 'string');

if ('import' === $func) {
    if (hr4you::import()) {
        echo rex_view::success(rex_i18n::msg('d2u_jobs_hr4you_import_success'));
    }
}

if ('' !== rex_config::get('d2u_jobs', 'hr4you_xml_url')) {
    echo "<a href='". rex_url::currentBackendPage(['func' => 'import']) ."'>"
            . "<button class='btn btn-apply'>". rex_i18n::msg('d2u_jobs_hr4you_import') .'</button></a>';
}
?>

<h2>XML Format</h2>
<p>HR4YOU stellt ein für den Kunden angepasstes XML bereit, das durch dieses
	Plugin importiert werden kann. Da sich das XML bei jedem Kunden unterscheiden
	kann, nachfolgend die Vorlage für eine XML Datei, die dieses Plugin importieren
	kann.</p>
<textarea style="width: 100%" rows="20">
<jobs>
	<entry id="1">
		<url_application_form>
			<![CDATA[ URL zur Onlinebewerbung ]]>
		</url_application_form>
		<titel>
			<![CDATA[ Titel ]]>
		</titel>
		<einleitung>
			<![CDATA[ Einleitung (z.B. Eintrittstermin: ... ]]>
		</einleitung>
		<gesamt_html>
			Stellenbeschreibung im HTML Format
		</gesamt_html>
		<gesamt>
			Stellenbeschreibung im Textformat
		</gesamt>
		<block1_html>
			<![CDATA[ Beschreibung der Aufgabenstellung ]]>
		</block1_html>
		<block2_html>
			<![CDATA[ Beschreibung des Bewerberprofils ]]>
		</block2_html>
		<block3_html>
			<![CDATA[ Beschreibung des Angebots ]]>
		</block3_html>
		<sprachcode><![CDATA[ de ]]></sprachcode>
		<von_datum>2015-10-09</von_datum>
		<bis_datum>2016-04-09</bis_datum>
		<plzarbeitsort>
			<![CDATA[ 11111 ]]>
		</plzarbeitsort>
		<arbeitsort>
			<![CDATA[ Wunderstadt ]]>
		</arbeitsort>
		<kennziffer>
			<![CDATA[ 8 ]]>
		</kennziffer>
		<firma>
			<![CDATA[ Name meiner Firma ]]>
		</firma>
		<ap_vorname>
			<![CDATA[ Vorname Kontaktperson ]]>
		</ap_vorname>
		<ap_nachname>
			<![CDATA[ Nachname Kontaktperson ]]>
		</ap_nachname>
		<ap_telefon>
			<![CDATA[ 0111/111-111 ]]>
		</ap_telefon>
		<ap_email>
			<![CDATA[ hr@meinefirma.de ]]>
		</ap_email>
		<berufskategorie_id>25</berufskategorie_id>
		<berufskategorie>
			<![CDATA[ Auszubildende/Studenten ]]>
		</berufskategorie>
		<stellenart_id>2</stellenart_id>
		<stellenart>
			<![CDATA[ Ausbildungsplatz ]]>
		</stellenart>
		<jobid>8</jobid>
		<stellenkategorie>1,2,4</stellenkategorie>
		<referenznummer>13</referenznummer>
		<kopfgrafik_name>Titel der Grafik</kopfgrafik_name>
		<kopfgrafik_url>
			http://meinefirma.hr4you.org/upload_files/upload_dateien/bild.jpg
		</kopfgrafik_url>
	</entry>
</jobs>
</textarea>
<h2>Automatischer Import</h2>
<p>Um einen automatischen Import zu installieren muss im Addon Cronjobs ein neuer
	Cronjob hinzugefügt werden. Als Typ wird URL Aufruf ausgewählt. Die URL ist
	die Sync URL das HR4YOU Plugins.</p>
<h2>Bug reporting</h2>
<p>Fehler bitte auf <a href="https://github.com/tobiaskrais/d2u_jobs/issues" target="_blank">GitHub</a> melden.</p>
<p>Fragen können im <a href="http://www.redaxo.org/de/forum/addons-f30/stellenmarkt-addon-mit-optionalem-hr4you-import-t20726.html" target="_blank">Redaxo Forum</a> gestellt werden.</p>
<h2>Changelog</h2>
<p>1.0.1:</p>
<ul>
	<li>Bugfix: Telefonnummer der Kontaktperson bleibt erhalten, wenn im Import
		keine angegeben ist.</li>
</ul>
<p>1.0.0:</p>
<ul>
	<li>Initiale Version.</li>
</ul>
