<?php

@define('PLUGIN_MODEMAINTAIN_TITLE', 'Wartungs-Modus');
@define('PLUGIN_MODEMAINTAIN_TITLE_DESC', 'Erlaubt das öffentliche Blog - das Frontend - in einen "503 - Service Temporarily Unavailable" Modus zu versetzen.');

@define('PLUGIN_MODEMAINTAIN_MAINTAIN', 'Service Wartungs-Modus');
@define('PLUGIN_DASHBOARD_MAINTENANCE_MODE_ACTIVE', '...aktiver Wartungsmodus...');
@define('PLUGIN_MODEMAINTAIN_INFOALERT', 'Achtung: Bitte Beschreibung lesen!');
@define('PLUGIN_MODEMAINTAIN_DASHBOARD_MODE_DESC', "ACHTUNG:<br>\n<b>Nicht</b> ausloggen, den Browser oder das Tab schließen, oder das generelle Konfigurations-Formular absenden, ohne den Wartungsmodus zurückgesetzt zu haben!");
@define('PLUGIN_MODEMAINTAIN_DASHBOARD_EXWARNING_DESC', "ACHTUNG:<br>\nEs <em>kann</em> eine (Browser-Session)-Situation geben, bei dem obiger 503-Submit-Knopf nach dem Absenden seine Farbe (grün/rot) nicht unmittelbar ändert, um anzuzeigen, in welchem Modus sich die Wartung gerade wirklich befindet. In diesem Fall können Sie den Knopf nach ca 1 Sekunde entweder erneut drücken, ODER besser auf derselben Browserseite irgendwohin ins Backend gehen und zurückkehren. Erst dann sehen Sie den augenblicklichen Status.");
@define('PLUGIN_MODEMAINTAIN_DASHBOARD_EMERGENCY_DESC', "IM NOTFALL:<br>\nWenn Sie sich jemals ausloggen, ohne den 503 Maintenance Mode zurückgestellt zu haben, oder ihr Login Cookie beschädigt oder gelöscht wurde, müssen Sie die &dollar;serendipity['maintenance'] Variable in der serendipity_config_local.inc.php Datei manuell auf 'false' stellen, um sich und der Öffentlichkeit wieder Zugang zu ihrem Blog zu ermöglichen!");

@define('PLUGIN_MODEMAINTAIN_MAINTAIN_NOTE', 'Öffentlicher Wartungs-Modus Text');
@define('PLUGIN_MODEMAINTAIN_MAINTAIN_TEXT', 'This site &#187;%s&#171; is currently undergoing some maintenance work and therefore is temporarily unavailable. Please visit us later.');
@define('PLUGIN_MODEMAINTAIN_MAINTAIN_USELOGO', 'Binde das Serendipity Logo ein?');

@define('PLUGIN_MODEMAINTAIN_BUTTON', 'Aktiviere den 503 Modus');
@define('PLUGIN_MODEMAINTAIN_FREEBUTTON', 'Zurücksetzen des 503 Modus');
@define('PLUGIN_MODEMAINTAIN_RETURN', 'Das Blog ist jetzt im %s Modus. <a href="%s">Zurück</a> zum Backend für die anstehenden Wartungsarbeiten.');

@define('PLUGIN_MODEMAINTAIN_TITLE_AUTOLOGIN', 'Um den Wartungsmodus administrieren zu können, müssen Sie sich abmelden und mit dem "Daten speichern" Modus wieder anmelden.');

@define('PLUGIN_MODEMAINTAIN_WARNLOGOFF', 'Hey, Sie sind im <span class="fivezerothree">503</span>-Wartungs-Modus! - <b>Befreien</b> Sie den Modus <a href="%s">hier</a>, <b>bevor</b> Sie sich abmelden!');

@define('PLUGIN_MODEMAINTAIN_HINT_MAINTENANCE_MODE', 'Wenn länger andauernd, oder mit möglichen Frontend-Auswirkungen verbunden, könnte dies eine gültige Aufgabe sein, den Wartungs-Modus zu nutzen!');

