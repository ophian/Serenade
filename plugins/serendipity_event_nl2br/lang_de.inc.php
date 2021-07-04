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
@define('PLUGIN_EVENT_NL2BR_ISOLATE_TAGS', 'Ausnahmen f�r alle folgenden Regeln');
@define('PLUGIN_EVENT_NL2BR_ISOLATE_TAGS_DESC', 'Eine Liste von benutzerdefinierten HTML-Tags, innerhalb derer keine Umbr�che konvertiert werden sollen. Konfigurationsvorschlag: "nl,pre,geshi,textarea". Trennen Sie mehrere HTML-Tags mit Komma. Hinweis: Die eingegebenen Tags sind regul�re Ausdr�cke!');
@define('PLUGIN_EVENT_NL2BR_PTAGS', 'Nutze P-Tags');
@define('PLUGIN_EVENT_NL2BR_PTAGS_DESC', 'Setze statt br-Tags p-Tags ein.');
@define('PLUGIN_EVENT_NL2BR_PTAGS_DESC2', 'Dies kann bei verschachtelten Markup-F�llen aber zu Fehlinterpretationen f�hren!');
@define('PLUGIN_EVENT_NL2BR_ISOBR_TAG', 'ISOBR Isolations-Default BR Einstellung');
@define('PLUGIN_EVENT_NL2BR_ISOBR_TAG_DESC', 'Mit dem funktionslosen NONE-HTML-Tag <nl> </nl> als NL2BR Isolations-Default Einstellung, kann die NL2BR Funktion nun so genutzt werden, dass alles innerhalb dieses Tags nicht von NL2BR geparst wird. Auch nicht verschachtelte mehrfach Vorkommen im Text werden unterst�tzt! Beispiel: <nl>do not parse newline to br inside this multiline-textblock</nl>');
@define('PLUGIN_EVENT_NL2BR_CLEANTAGS', 'Nutze BR-Clean-Tags fallback, wenn ISOBR false');
@define('PLUGIN_EVENT_NL2BR_CLEANTAGS_DESC', 'Bei Benutzung von <HTML-Tags> in den Eintr�gen, die nicht zufriedenstellend mit der ISOBR Config-Option gel�st werden k�nnen, l�sche nl2br Umbruch nach <tag>. Dies gilt f�r alle <tags>, die mit > oder >\n enden! Default (table|thead|tbody|tfoot|th|tr|td|caption|colgroup|col|ol|ul|li|dl|dt|dd)');
@define('PLUGIN_EVENT_NL2BR_CONFIG_ERROR', 'Konfigurations Fehler! Die Option: "%s" wurde zur�ckgesetzt, weil die Option \'%s\' aktiv geschaltet war! Benutzen sie bitte nur eine dieser Optionen.');

@define('PLUGIN_EVENT_NL2BR_ABOUT_TITLE', 'BITTE BEACHTEN Sie die Auswirkungen dieses Markup-Plugins:');
@define('PLUGIN_EVENT_NL2BR_ABOUT_DESC', '<p>Dieses Plugin �bertr�gt Zeilenumbr�che in HTML-Zeilenumbr�che, so dass sie in Ihrem Blog-Eintrag erscheinen.</p>
<p><b><u>Vorbemerkung</u>:</b> Die Serendipity Standard Auslieferung nutzt per default seit jeher keine anderen Markup Plugins. Diese Textform nennen wir hier PLAIN (TEXT) EDITOR. Text ist reiner Text und per ENTER oder strukturell eingef�gte Zeilenumbr�che werden kodiert in der Datenbank gespeichert und durch dieses Plugin erst bei Ausgabe zur Laufzeit in HTML verwandelt.</p>
<p><b>PLAIN EDITOR</b>s Basis-Funktionalit�t: Konvertiere die Zeilenumbr�che zu &lt;br&gt; - Tags.<br>
<b>PLAIN EDITOR</b>s Erweiterte Funktionalit�t: Parse den Text in &lt;p&gt;-Tags unter Ber�cksichtigung der HTML-Syntax wo sie erlaubt sind und automatische Ignorierung bei vorformatiertem Text mit &lt;pre&gt; oder innerhalb von &lt;style&gt; oder &lt;svg&gt;-Tags.</p>
<p>Dies kann insbesondere dann f�r Sie zu Problemen f�hren, wenn Sie w�hrend des Betriebs ihres Blogs das Markup-Plugin wechseln, danach also Inhalte mit unterschiedlichen Anforderungen in den Eintragstabellen zu finden sind:</p>
<ul>
    <li>Der eingebaute <strong>WYSIWYG-Editor</strong> und das <strong>CKEditor Plus</strong> Plugin speichern bereits korrektes HTML - bereit zur Ausgabe - und schalten automatisch das NL2BR Plugin f�r die Ausgabe ab. (Ansonsten g�be es eine Verdopplung aller codierten Zeilenumbr�che und w�rde das Ausgabelayout zumindest ver�ndern oder sogar zerst�ren.)</li>
    <li>Wenn Sie andere Markup-Plugins in Verbindung mit diesem Plugin verwenden, die bereits Zeilenumbr�che �bersetzen. Die <strong>TEXTILE</strong>- und <strong>MARKDOWN</strong>-Plugins sind Beispiele f�r solche Plugins. (Auch f�r diese beiden gibt es entsprechende Vorkehrungen zur Abschaltung von NL2BR.)</li>
</ul>
<p>Dieses "<em>Problem</em>" gilt in hohem Ma�e aber nur, wenn sie sehr alte Eintr�ge aus der Fr�hzeit von Serendipity haben, bei denen der Markup Zustand bzw. die NL2BR-Anforderung nicht entsprechend hinterlegt wurden.</p>
<p>Um weitere Probleme zu vermeiden, sollten Sie das nl2br-Plugin entweder f�r Eintr�ge global oder per Eintrag im Abschnitt "Erweiterte Eigenschaften" eines Eintrags deaktivieren, wenn Sie das Plugin f�r die Eingabeeigenschaften (entryproperties) installiert haben.</p>
<p><u>Genereller Hinweis:</u> Das nl2br Plugin ist also nur wirklich sinnvoll, wenn Sie</p>
<ul>
    <li>keine anderen Markup-Plugins verwenden - oder</li>
    <li>keinen WYSIWYG-Editor verwenden - oder</li>
    <li>lediglich Linebreak-Transformationen auf Kommentare zu Ihren Blog-Eintr�gen anwenden m�chten, und keine m�glichen Markups anderer Plugins zulassen, die Sie nur f�r Blogeintr�ge verwenden.</li>
</ul>
<p>NL2BR ist ein Kurzform-Wort. Lese als: Funktion "NL zu BR", <b>nicht</b> "NL zwei BR"!</p>');

