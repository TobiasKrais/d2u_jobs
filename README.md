# D2U Jobs Addon

Dieses Addon wurde durch das Stellenmarkt Addon (Addon Key: jobs) abgelöst. Für den Umstieg bitte folgende Anpassungen vornehmen:

- Für die automatische Datenübernahme das d2u_jobs Addon auf Version 2.0.0 aktualisieren.
- Die Beispielmodule werden automatisch aktualisiert. Sind eigene Module vorhangen, dann bitte folgende Anpassungen vornehmen:
  - Das Prefix "d2u_" überall entfernen:
    - Aus den Datenbanktabellennamen den Prefix "d2u_" entfernen.
    - Aus den Sprog Wildcards das Prefix "d2u_" entfernen.
    - Aus dem Media Manager Effekt das Prefix "d2u_" entfernen.
    - Aus dem key der rex_config Werten das Prefix "d2u_" entfernen.
  - Klassennamen anpassen:
    - D2U_Jobs\Category wird zu FriendsOfRedaxo\Jobs\Category.
    - D2U_Jobs\Contact wird zu FriendsOfRedaxo\Jobs\Contact.
    - D2U_Jobs\Job wird zu FriendsOfRedaxo\Jobs\Job.
    - D2U_Jobs\hr4you wird zu FriendsOfRedaxo\Jobs\Hr4youImport.
    - d2u_jobs_frontend_helper wird zu FriendsOfRedaxo\Jobs\FrontendHelper
