<?php

/**
 *  @version 1.0
 *  @author Ian
 *  @translated 2017/10/09
 */

@define('PLUGIN_CLEANSPAM_NAME', 'Wartungs-Cleanup der Spam Logs');
@define('PLUGIN_CLEANSPAM_DESC', 'R�umt Datenbank-Logeintr�ge auf, die bestimmten Kriterien unterliegen. Dies sollte periodisch angesto�en werden, da Spammer die Spamblog Logs kontinuierlich aufbl�hen und das Blog immer weiter verlangsamen.');
@define('PLUGIN_CLEANSPAM_MAINTAIN', 'Aufr�umen der Spam-Logs');

@define('PLUGIN_CLEANSPAM_ALL_BUTTON', 'L�sche: Alle');
@define('PLUGIN_CLEANSPAM_ALL_DESC', 'L�sche <b>alle</b> Log-Eintr�ge des Typs: \'REJECTED\' in der Datenbank.Tabelle "spamblocklog". Augenblicklich ("%d") enthalten.');
@define('PLUGIN_CLEANSPAM_MULTI_BUTTON', 'L�sche: selektiv');
@define('PLUGIN_CLEANSPAM_YEARS_BUTTON', 'L�sche: Jahre');
@define('PLUGIN_CLEANSPAM_MULTI_DESC', 'L�sche individuell <b>multi</b>-selected oder <b>Einzeln</b> von Tabellenfeld "reason" LIKE "items"');
@define('PLUGIN_CLEANSPAM_MSG_DONE', 'L�schung erfolgt!');
@define('PLUGIN_CLEANSPAM_SELECT', 'Einzelselektion nach Kriterien');
@define('PLUGIN_CLEANSPAM_VISITORS', 'L�sche Besucher nach Jahren');
@define('PLUGIN_CLEANSPAM_VISITORS_DESC', 'Multi-select <b>Jahre</b>, um die Datenbank Log-Tabelle "visitors" der Besucher zu s�ubern. <u>ACHTUNG: </u><br>Dies beeinflusst die Statistik Historie des Statistik-Ereignis-Plugins.');

