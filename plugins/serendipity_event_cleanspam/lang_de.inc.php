<?php

/**
 *  @version 1.0
 *  @author Ian
 *  @translated 2017/10/09
 */

@define('PLUGIN_CLEANSPAM_NAME', 'Wartungs-Cleanup der Spam Logs');
@define('PLUGIN_CLEANSPAM_INFO', 'INFO');
@define('PLUGIN_CLEANSPAM_INFO_DESC', 'R�umt Datenbank-Logeintr�ge auf, die bestimmten Kriterien unterliegen. Dies sollte periodisch angesto�en werden, da Spammer die Spamblog Logs kontinuierlich aufbl�hen und das Blog immer weiter verlangsamen. Achten Sie darauf, dass vorher all diejenigen Kommentare genehmigt wurden, die einen MODERIEREN Status haben! Erfahrungsgem�� sind, je nach Einstellung der Spamblog Plugins, diesen beiden Typen haupts�chlich mit Spam best�ckt.');
@define('PLUGIN_CLEANSPAM_MAINTAIN', 'Aufr�umen der Spam-Logs');

@define('PLUGIN_CLEANSPAM_ALL_BUTTON', 'L�sche: Alle');
@define('PLUGIN_CLEANSPAM_ALL_DESC', 'L�sche <b>alle</b> Log-Eintr�ge des Typs: \'REJECTED\' und \'MODERATE\' in der Datenbank.Tabelle "spamblocklog". Augenblicklich sind ("%d") Eintr�ge enthalten.');
@define('PLUGIN_CLEANSPAM_MULTI_BUTTON', 'L�sche: Selektiv');
@define('PLUGIN_CLEANSPAM_YEARS_BUTTON', 'L�sche: Jahre');
@define('PLUGIN_CLEANSPAM_MULTI_DESC', 'L�sche <b>Einzeln</b> oder per <b>Mehrfachauswahl</b> vom Tabellenfeld "reason" LIKE "items". Gilt f�r die Typen: \'REJECTED\' und \'MODERATE\'!');
@define('PLUGIN_CLEANSPAM_MSG_DONE', 'L�schung erfolgt!');
@define('PLUGIN_CLEANSPAM_SELECT', 'Einzelselektion nach Kriterien');
@define('PLUGIN_CLEANSPAM_VISITORS', 'L�sche Besucher nach Jahren');
@define('PLUGIN_CLEANSPAM_VISITORS_DESC', 'W�hle einzelne oder mehrere <b>Jahre</b>, um die Datenbank Log-Tabelle "visitors" der Besucher zu s�ubern. <u>ACHTUNG:</u><br>Dies beeinflusst die Statistik Historie des Statistik-Ereignis-Plugins.');

