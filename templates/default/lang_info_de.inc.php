<?php
/*
 * The info.txt charset (translation) file as an array.
 * Save as lang_info_xx.inc.php and replace 'xx' with your short lang term, defined and set in $serendipity['lang'].
 * Convert or save as ANSI (ISO-8859-1) or your native charset
 **/

$info['theme_info_summary'] = 'Das Serendipity Styx Basis-Template.';

$info['theme_info_desc'] = 'Als HTML5 Frontend Theme neu �berarbeitet f�r Styx um es voll "Responsiv" zu machen (3-2-1),
ohne dabei allzuviel von seinem alten HTML(4) Markup zu �ndern.<br>
Es arbeitet als vollwertiges "Fallback" f�r die PHP- und XML-Engine und als allgemeiner Datei-Reserve-Pool f�r alle sonstigen Themes.<br>
<br>
Im Unterschied zu den Serendipity <b>Standard</b> Templates (fr�her "Bulletproof", sp�ter "2k11", jetzt "Pure &lsaquo; 2020 &rsaquo;") dient dieses Theme als grundlegende System Basis
und als allgemeines Backup- und "R�ckfall"-Theme, solange nicht spezielle Anweisungen (*) oder interne Gr�nde etwas anderes erzwingen,
zB. wenn etwas zur Smarty-Kompilierung gesucht und nicht in der �blichen Theme oder Fallback-Theme-Kaskade gefunden wurde.<br>
<br>
<span class="footnote">[*] Das hei�t, wenn keine "Engine" (in der info.txt) und keine eigene Serendipity Stylesheet-Datei (style.css)
gesetzt sind, wie es zB. f�r das "Default-php" oder "Default-xml" Theme der Fall ist.</span>';

$info['theme_info_backend'] = 'Dieses Theme beherbergt die Styx-Kern Backend Templates im Unterverzeichnis "default/admin".
Die Template-Dateien in diesem Verzeichnis bilden und erstellen das Aussehen der kompletten Admin-Oberfl�che.
Sie enthalten auch einige Workflow- und Logikfunktionen sowie eigene Javascript-Bibliotheken.
Wenn Sie ein eigenes Backend-Theme verwenden m�chten, kopieren Sie das Verzeichnis "admin" in ihr Theme.
�ndern Sie dort die Datei "info.txt", um eine Zeile "Backend&colon; Yes" hinzuzuf�gen, und w�hlen Sie das neue Backend-Theme in der neu geladenen Themenliste aus.
Ab sofort k�nnen Sie die Dateien und Stile des eigenen Backend-Themes bearbeiten und an Ihre erweiterten Bed�rfnisse anpassen.<br>
<u><b>Bitte beachten Sie:</b></u> Dies ist nur f�r erfahrene Benutzer empfehlenswert und komplett abseits der Update-Funktionen!';
