<?php

/**
 *  @version  
 *  @file 
 *  @author 
 *  DE-Revision: Revision of lang_de.inc.php
 */

@define('PLUGIN_EVENT_NL2BR_NAME', 'Textformatierung: NL2BR');
@define('PLUGIN_EVENT_NL2BR_DESC', 'Konvertiert Zeilenumbr�che zu HTML');
@define('PLUGIN_EVENT_NL2BR_CHECK_MARKUP', '�berpr�fe Markup-Plugins?');
@define('PLUGIN_EVENT_NL2BR_CHECK_MARKUP_DESC', '�berpr�ft automatisch auf existierende Markup-Plugins, um die weitere Ausf�hrung des NL2BR-Plugins zu untersagen. Dies gilt dann, wenn WYSIWYG oder spezifische Markup-Plugins entdeckt werden.');
@define('PLUGIN_EVENT_NL2BR_ISOLATE_TAGS', 'Ausnahmen von dieser Regel');
@define('PLUGIN_EVENT_NL2BR_ISOLATE_TAGS_DESC', 'Eine Liste von HTML-Tags, innerhalb derer keine Umbr�che bei Benutzung von P-Tags konvertiert werden. Konfigurationsvorschlag: "code,pre,geshi,textarea". Trennen Sie mehrere HTML-Tags mit Komma. Hinweis: Die eingegebenen Tags sind regul�re Ausdr�cke!');
@define('PLUGIN_EVENT_NL2BR_PTAGS', 'Nutze P-Tags');
@define('PLUGIN_EVENT_NL2BR_PTAGS_DESC', 'Setze statt br-Tags p-Tags ein.');
@define('PLUGIN_EVENT_NL2BR_PTAGS_DESC2', 'Dies kann bei verschachtelten Markup-F�llen aber zu Fehlinterpretationen f�hren!');
@define('PLUGIN_EVENT_NL2BR_ISOBR_TAG', 'ISOBR Isolations-Default BR Einstellung');
@define('PLUGIN_EVENT_NL2BR_ISOBR_TAG_DESC', 'Mit dem neu eingef�gten NON-HTML-Tag <nl> </nl> als NL2BR Isolations-Default Einstellung, kann die NL2BR Funktion nun so genutzt werden, dass alles innerhalb dieses Tags nicht von NL2BR geparst wird. Auch nicht verschachtelte mehrfach Vorkommen im Text werden unterst�tzt! Beispiel: <nl>do not parse newline to br inside</nl>');
@define('PLUGIN_EVENT_NL2BR_CLEANTAGS', 'Nutze BR-Clean-Tags fallback, wenn ISOBR false');
@define('PLUGIN_EVENT_NL2BR_CLEANTAGS_DESC', 'Bei Benutzung von <HTML-Tags> in den Eintr�gen, die nicht zufriedenstellend mit der ISOBR Config-Option gel�st werden k�nnen, l�sche nl2br Umbruch nach <tag>. Dies gilt f�r alle <tags>, die mit > oder >\n enden! Default (table|thead|tbody|tfoot|th|tr|td|caption|colgroup|col|ol|ul|li|dl|dt|dd)');
@define('PLUGIN_EVENT_NL2BR_CONFIG_ERROR', 'Konfigurations Fehler! Die Option: "%s" wurde zur�ckgesetzt, weil die Option \'%s\' aktiv geschaltet war! Benutzen sie bitte nur eine dieser Optionen.');

@define('PLUGIN_EVENT_NL2BR_ABOUT_TITLE', 'BITTE BEACHTEN Sie die Auswirkungen dieses Markup-Plugins:');
@define('PLUGIN_EVENT_NL2BR_ABOUT_DESC', '<p>Dieses Plugin �bertr�gt Zeilenumbr�che in HTML-Zeilenumbr�che, so dass sie in Ihrem Blog-Eintrag erscheinen.</p>
<p>In zwei F�llen kann dies f�r Sie zu Problemen f�hren:</p>
<ul>
    <li>wenn Sie zuvor einen <strong>WYSIWYG-Editor</strong> zum Schreiben Ihrer Eintr�ge verwendet haben. In diesem Fall hat der WYSIWYG-Editor bereits korrekte HTML-Zeilenumbr�che platziert, so dass das nl2br-Plugin diese Zeilenumbr�che eigentlich verdoppeln t�te. Seit <strong>Serendipity 2.0</strong> braucht man sich darum, in Blogeintr�gen und statischen Seiten, aber nicht mehr zu k�mmern, da der nl2br Parser automatisch erkannt und deaktiviert wird.</li>
    <li>wenn Sie andere Markup-Plugins in Verbindung mit diesem Plugin verwenden, die bereits Zeilenumbr�che �bersetzen. Die <strong>TEXTILE</strong>- und <strong>MARKDOWN</strong>-Plugins sind Beispiele f�r solche Plugins.</li>
</ul>
<p>Um Probleme zu vermeiden, sollten Sie das nl2br-Plugin f�r Eintr�ge global oder per Eintrag im Abschnitt "Erweiterte Eigenschaften" eines Eintrags deaktivieren, wenn Sie das Plugin f�r die Eingabeeigenschaften (entryproperties) installiert haben.</p>
<p><u>Genereller Hinweis:</u> Das nl2br Plugin ist also nur wirklich sinnvoll, wenn Sie</p>
<ul>
    <li>keine anderen Markup-Plugins verwenden - oder</li>
    <li>keinen WYSIWYG-Editor verwenden - oder</li>
    <li>lediglich Linebreak-Transformationen auf Kommentare zu Ihren Blog-Eintr�gen anwenden m�chten, und keine m�glichen Markups anderer Plugins zulassen, die Sie nur f�r Blogeintr�ge verwenden.</li>
</ul>
<p>NL2BR ist ein Kurzform-Wort. Sprich, lese als: Funktion NL zu BR, <b>nicht</b> NL zwei BR!</p>');

