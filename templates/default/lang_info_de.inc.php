<?php
/*
 * The info.txt charset (translation) file as an array.
 * Save as lang_info_xx.inc.php and replace 'xx' with your short lang term, defined and set in $serendipity['lang'].
 * Convert or save as ANSI (ISO-8859-1) or your native charset
 **/

$info['theme_info_summary'] = 'Das Styx Default Template, vormals Serendipity v2.3 genannt (in der Numerierung ohne Bezug auf die Serendipity Version).';

$info['theme_info_desc'] = 'Als Frontend Theme neu �berarbeitet f�r Styx 2.1+, um es voll "Responsiv" zu machen (3-2-1),
ohne viel von seinem alten HTML(4) Markup zu �ndern.
Es arbeitet au�erdem als vollwertiges "Fallback" f�r die PHP- und XML-Engine
und als allgemeiner Datei-Reserve-Pool f�r alle sonstigen Themes.
Dieses Template war zuvor nicht dazu bestimmt, eine Vorlage gleich vielen anderen zu sein.
Da "Bulletproof" und sp�ter "2k11" die Serendipity <b>Standard</b> Templates wurden,
bekam dieses Template seine Aufgabe als default Backend, als Backup- und "R�ckfall"-Theme zugewiesen,
wenn etwas zur Smarty-Kompilierung gesucht und nicht in der �blichen Theme
oder Fallback-Theme-Kaskade gefunden wurde. Es wird aber auch gew�hlt,
wenn keine "Engine" (in der info.txt) und keine eigene Stylesheet-Datei (style.css)
gesetzt sind, wie es zB. f�r die Default-php und/oder Default-xml-Theme der Fall ist.';

$info['theme_info_backend'] = 'Dieses Theme beherbergt die Styx-Kern Backend Templates im Unterverzeichnis "default/admin".
Die Template-Dateien in diesem Verzeichnis bilden und erstellen das Aussehen der kompletten Admin-Oberfl�che.
Sie enthalten auch einige Workflow- und Logikfunktionen sowie eigene Javascript-Bibliotheken.
Wenn Sie ein eigenes Backend-Theme verwenden m�chten, kopieren Sie das Verzeichnis "admin" in ihr Theme.
�ndern Sie dort die Datei "info.txt", um eine Zeile "Backend&colon; Yes" hinzuzuf�gen, und w�hlen Sie das neue Backend-Theme in der neu geladenen Themenliste aus.
Ab sofort k�nnen Sie die Dateien und Stile des eigenen Backend-Themes bearbeiten und an Ihre erweiterten Bed�rfnisse anpassen.<br>
<u><b>Bitte beachten Sie:</b></u> Dies ist nur f�r erfahrene Benutzer empfehlenswert und komplett abseits der Update-Funktionen!';
