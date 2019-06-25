{foreach $media.files AS $file}
    {if NOT $media.manage}
        {* ML got called for inserting media *}
            {if $file.is_image AND isset($file.full_thumb_path) AND $file.full_thumb_path}
                {if NOT empty($media.textarea) OR NOT empty($media.htmltarget)}
                {$link="?serendipity[adminModule]=images&amp;serendipity[adminAction]=choose&amp;serendipity[fid]={$file.id}&amp;serendipity[textarea]={$media.textarea}&amp;serendipity[noBanner]=true&amp;serendipity[noSidebar]=true&amp;serendipity[noFooter]=true&amp;serendipity[filename_only]={$media.filename_only}&amp;serendipity[htmltarget]={$media.htmltarget}"}
                {else}
                    {if $file.url}
                        {$link="{$file.url}&amp;serendipity[image]={$file.id}"}
                    {/if}
                {/if}

                {$img_src_webp="{$file.full_thumb_webp|default:''}"}
                {$img_src="{$file.full_thumb}"}
                {$img_title="{$file.path}{$file.name}"}
                {$img_alt="{$file.realname}"}

            {elseif $file.is_image AND $file.hotlink}
                {if NOT empty($media.textarea)}
                    {$link="?serendipity[adminModule]=images&amp;serendipity[adminAction]=choose&amp;serendipity[fid]={$file.id}&amp;serendipity[textarea]={$media.textarea}&amp;serendipity[noBanner]=true&amp;serendipity[noSidebar]=true&amp;serendipity[noFooter]=true&amp;serendipity[filename_only]={$media.filename_only}&amp;serendipity[htmltarget]={$media.htmltarget}"}
                {else}
                    {if $file.url}
                        {$link="{$file.url}&amp;serendipity[image]={$file.id}"}
                    {/if}
                {/if}

                {* $link_webp="{$file.full_file_webp|default:''}" NOT actually a NEED here, isn't it .. *}
                {* $img_src_webp="{$file.full_thumb_webp|default:''}" NOT actually a NEED here, isn't it .. *}
                {$img_src="{$file.path}"}
                {$img_title="{$file.path}"}
                {$img_alt="{$file.realname}"}
            {else}
                {if NOT empty($media.textarea)}
                    {$link="?serendipity[adminModule]=images&amp;serendipity[adminAction]=choose&amp;serendipity[fid]={$file.id}&amp;serendipity[textarea]={$media.textarea}&amp;serendipity[noBanner]=true&amp;serendipity[noSidebar]=true&amp;serendipity[noFooter]=true&amp;serendipity[filename_only]={$media.filename_only}&amp;serendipity[htmltarget]={$media.htmltarget}"}
                {else}
                    {if $file.url}
                        {$link="{$file.url}&amp;serendipity[image]={$file.id}"}
                    {/if}
                {/if}

                {$img_src="{$file.mimeicon}"}
                {$img_title="{$file.path}{$file.name}({$file.mime})"}
                {$img_alt="{$file.mime}"}
            {/if}
    {else}
        {if $file.is_image AND isset($file.full_thumb_path) AND $file.full_thumb_path}
            {$link="{if $file.hotlink}{$file.path}{else}{$file.full_file}{/if}"}
            {$link_webp="{$file.full_file_webp|default:''}"}
            {$img_src="{$file.show_thumb}"}
            {$img_src_webp="{$file.full_thumb_webp|default:''}"}
            {$img_title="{$file.path}{$file.name}"}
            {$img_alt="{$file.realname}"}
        {elseif $file.is_image AND $file.hotlink}
            {$link="{if $file.hotlink}{$file.path}{else}{$file.full_file}{/if}"}
            {$link_webp="{$file.full_file_webp|default:''}"}
            {$img_src="{$file.path}"}
            {$img_title="{$file.path}"}
            {$img_alt="{$file.realname}"}
        {else}
            {$link="{if $file.hotlink}{$file.path}{else}{$file.full_file}{/if}"}
            {$link_webp="{$file.full_file_webp|default:''}"}
            {$img_src="{$file.mimeicon}"}
            {$img_src_webp="{$file.full_thumb_webp|default:''}"}
            {$img_title="{$file.path}{$file.name}({$file.mime})"}
            {$img_alt="{$file.mime}"}
        {/if}
    {/if}
    {* builds a ML objects link for step 1, to pass to media_choose.tpl file section: passthrough media.filename_only scripts - do not use "empty($link) AND" here, since that would require a reset before! Strictly build this link for media to textarea cases only. *}
    {if (!$file.is_image OR $file.is_image == 0) AND $file.mediatype != 'image' AND $file.realfile AND !empty($media.textarea) AND !empty($media.htmltarget)}
        {$link="?serendipity[adminModule]=images&amp;serendipity[adminAction]=choose&amp;serendipity[noBanner]=true&amp;serendipity[noSidebar]=true&amp;serendipity[noFooter]=true&amp;serendipity[fid]={$file.id}&amp;serendipity[filename_only]={$media.filename_only}&amp;serendipity[textarea]={$media.textarea}&amp;serendipity[htmltarget]={$media.htmltarget}"}
    {/if}

            <article id="media_{$file.id}" class="media_file{if !empty($smarty.get.serendipity.adminAction) AND $smarty.get.serendipity.adminAction == 'properties'} mfile_prop{/if} {if $media.manage AND $media.multiperm}manage {/if}{cycle values="odd,even"}">
                <header class="clearfix">
                    {if $media.manage AND $media.multiperm}

                    <div class="form_check">
                        <input id="multicheck_image{$file.id}" class="multicheck" name="serendipity[multiCheck][]" type="checkbox" value="{$file.id}" data-multixid="media_{$file.id}">
                        <label for="multicheck_image{$file.id}" class="visuallyhidden">{$CONST.TOGGLE_SELECT}</label>
                    </div>
                    {/if}

                    <h3 title="{$file.diskname}">{if $media.manage}{$file.diskname|truncate:38:"&hellip;":true}{else}{$file.diskname}{/if}{if !empty($file.orderkey)}: {$file.orderkey|escape}{/if}</h3>
                    {if $file.authorid != 0}<span class="author block_level">{$file.authorname}</span>{/if}

                </header>

                <div class="clearfix equal_heights media_file_wrap">
                    <div class="media_file_preview">
                        {if isset($link)}
                        <a{if $media.manage AND $media.viewperm} class="media_fullsize"{/if} href="{$link_webp|default:$link}" data-fallback="{$link}" title="{$CONST.MEDIA_FULLSIZE}: {$file.diskname}" data-pwidth="{$file.popupWidth}" data-pheight="{$file.popupHeight}">
                            <picture>
                                <source type="image/webp" srcset="{$img_src_webp|default:''}" class="ml_preview_img" alt="{$img_alt}">
                                <img src="{$img_src}" title="{$img_title}" alt="{$img_alt}"><!-- media/manage -->
                            </picture>
                        </a>
                        {else}
                        {if $file.is_image}
                        <picture>
                            <source type="image/webp" srcset="{$img_src_webp|default:''}" class="ml_preview_img" alt="{$img_alt}">
                            <img src="{$img_src}" title="{$img_title}" alt="{$img_alt}"><!-- media/properties -->
                        </picture>
                        {if $file.mime|truncate:6:'' == 'image/' AND ($file.extension|count_characters > $CONST.PATHINFO_EXTENSION)}
                        <span class="msg_error"><span class="icon-attention-circled" aria-hidden="true"></span> {$CONST.ERROR_SOMETHING}
                            <p>{$CONST.MEDIA_EXTENSION_FAILURE|sprintf:$file.realname:$file.mime:$file.extension:($file.extension|count_characters):$CONST.PATHINFO_EXTENSION}</p>
                            {$CONST.MEDIA_EXTENSION_FAILURE_REPAIR}
                        </span>
                        {/if}
                        {else}
                        {if {$file.mime|regex_replace:"/\/.*$/":""} == 'video' AND in_array($file.extension, ['mp4', 'webm', 'ogv'])}{* * *}
                        <video width="320" height="240" controls>
                            <source src="{$file.full_file}"  type="video/{$file.extension}"><!-- media/properties video -->
                        </video>
                        {else}
                         <img src="{$img_src}" title="{$img_title}" alt="{$img_alt}"><!-- media/properties non image file -->
                        {/if}
                        {/if}
                        {/if}
                        <footer id="media_file_meta_{$file.id}" class="media_file_meta additional_info">
                            <ul class="plainList">
                            {if $file.hotlink}

                                <li><b>{$CONST.MEDIA_HOTLINKED}:</b> {$file.nice_hotlink}</li>
                            {else}
                                {if $file.realname != $file.diskname}

                                <li title="{$file.realname}"><b>Origin:</b> {$file.realname|truncate:38:"&hellip;"}</li>
                                {/if}
                                {if $file.mime}

                                <li><b>MIME-{$CONST.TYPE}:</b> {$file.mime}</li>
                                {/if}
                                {if $file.is_image}

                                <li><b>{$CONST.ORIGINAL_SHORT}:</b> {$file.dimensions_width}x{$file.dimensions_height}</li>
                                <li><b>{$CONST.THUMBNAIL_SHORT}:</b> {$file.dim.0}x{$file.dim.1}</li>
                                {/if}

                                <li><b>{if $file.is_image}{$CONST.IMAGE_SIZE}{else}{$CONST.SORT_ORDER_SIZE}{/if}:</b> {$file.nice_size} KB</li>
                                {if isset($file.nice_thumbsize) AND NOT $file.hotlink}
                                <li><b>{$CONST.THUMBFILE_SIZE}:</b> {$file.nice_thumbsize} KB</li>
                                {/if}
                                <li><b>{$CONST.PATH}:</b> "{$file.path}"</li>
                                <li><b>{$CONST.DATE}:</b> {$file.date|formatTime:DATE_FORMAT_SHORT}</li>
                            {/if}

                            </ul>
                        </footer>
                    </div>
                </div>
            {if (($media.manage OR {serendipity_getConfigVar key='showMediaToolbar'}) AND $media.metaActionBar)}

                <ul class="media_file_actions actions plainList clearfix">
                    <li><a class="media_show_info button_link" href="#media_file_meta_{$file.id}" title="{$CONST.SHOW_METADATA}"><span class="icon-info-circled" aria-hidden="true"></span><span class="visuallyhidden"> {$CONST.SHOW_METADATA}</span></a></li>
                {if $file.is_editable}
                    {if $file.is_image AND NOT $file.hotlink AND $media.resetperm}

                    <li><button class="media_rename button_link" type="button" title="{$CONST.MEDIA_RENAME}" data-fileid="{$file.id}" data-filename="{$file.name|escape:javascript}"><span class="icon-edit" aria-hidden="true"></span><span class="visuallyhidden"> {$CONST.MEDIA_RENAME}</span></button></li>
                    {/if}
                    {if $file.is_image AND NOT $file.hotlink AND $media.multiperm}

                    <li><a class="media_resize button_link" href="?serendipity[adminModule]=images&amp;serendipity[adminAction]=scaleSelect&amp;serendipity[fid]={$file.id}{if isset($smarty.get.serendipity.page)}&amp;serendipity[page]={$smarty.get.serendipity.page}{/if}" title="{$CONST.IMAGE_RESIZE}"><span class="icon-resize-full" aria-hidden="true"></span><span class="visuallyhidden"> {$CONST.IMAGE_RESIZE}</span></a></li>
                    {/if}
                    {if $file.is_image AND NOT $file.hotlink AND $media.multiperm}

                    <li><a class="media_rotate_left button_link" href="?serendipity[adminModule]=images&amp;serendipity[adminAction]=rotateCCW&amp;serendipity[fid]={$file.id}" title="{$CONST.IMAGE_ROTATE_LEFT}"><span class="icon-ccw" aria-hidden="true"></span><span class="visuallyhidden"> {$CONST.IMAGE_ROTATE_LEFT}</span></a></li>
                    {/if}
                    {if $file.is_image AND NOT $file.hotlink AND $media.multiperm}

                    <li><a class="media_rotate_right button_link" href="?serendipity[adminModule]=images&amp;serendipity[adminAction]=rotateCW&amp;serendipity[fid]={$file.id}" title="{$CONST.IMAGE_ROTATE_RIGHT}"><span class="icon-cw" aria-hidden="true"></span><span class="visuallyhidden">{$CONST.IMAGE_ROTATE_RIGHT}</span></a></li>
                    {/if}
                    {if $media.manage AND $media.multiperm}

                    <li><a class="media_prop button_link" href="?serendipity[adminModule]=images&amp;serendipity[adminAction]=properties&amp;serendipity[fid]={$file.id}{if isset($smarty.get.serendipity.page)}&amp;serendipity[page]={$smarty.get.serendipity.page}{/if}" title="{$CONST.MEDIA_PROP}"><span class="icon-picture" aria-hidden="true"></span><span class="visuallyhidden"> {$CONST.MEDIA_PROP}</span></a></li>
                    {/if}
                    {if $media.multiperm OR 'adminImagesDelete'|checkPermission}

                    <li><a class="media_delete button_link" href="?serendipity[adminModule]=images&amp;serendipity[adminAction]=delete&amp;serendipity[fid]={$file.id}" title="{$CONST.MEDIA_DELETE}" data-fileid="{$file.id}" data-filename="{$file.name|escape:javascript}"><span class="icon-trash" aria-hidden="true"></span><span class="visuallyhidden"> {$CONST.MEDIA_DELETE}</span></a></li>
                    {/if}
                    {if !empty($imagesNoSync)}
                    {foreach $imagesNoSync AS $special}
                    {if $file.name == $special.pfilename}

                    <li class="special"><a class="media_fullsize media_prop button_link" href="{$special.url}" title="{if $special.extension == 'webp'}{$CONST.VARIATION|default:'Image variation'}{else}{$CONST.PUBLISHED}{/if}: {$special.basename}, {$special.width}x{$special.height}px" data-pwidth="{$special.width}" data-pheight="{$special.height}"><span class="icon-image-of" aria-hidden="true">&#x22b7;</span><span class="visuallyhidden"> Image Of</span></a></li>
                    {/if}
                    {/foreach}
                    {/if}
                {/if}

                </ul>
            {/if}

            </article>

        {if NOT $media.enclose}

            <article class="media_file{if !empty($smarty.get.serendipity.adminAction) AND $smarty.get.serendipity.adminAction == 'properties'} mfile_prop{/if} media_enclose_no">
                <header>
                    <h3>{$file.realname}</h3>
                    <div>
                        <span class="block_level"><b>MIME-{$CONST.TYPE}:</b> {$file.mime}{if $file.realname != $file.diskname}, {$file.diskname}{/if}</span>
                        <span class="block_level"><b>{$CONST.SORT_ORDER_EXTENSION}:</b> {$file.extension}</span>
                        <ul class="media_file_meta plainList">
                            <li><b>{$CONST.SORT_ORDER_DATE}:</b> {if $file.authorid != 0}{$CONST.POSTED_BY} {$file.authorname} {/if}{$CONST.ON} {$file.date|formatTime:DATE_FORMAT_SHORT}</li>
                        {if $file.hotlink}

                            <li><b>{$CONST.MEDIA_HOTLINKED}:</b> {$file.nice_hotlink}</li>
                        {elseif $file.is_image}

                            <li><b>{$CONST.ORIGINAL_SHORT}:</b> {$file.dimensions_width}x{$file.dimensions_height} px</li>
                            <li><b>{$CONST.THUMBNAIL_SHORT}:</b> {$file.dim.0}x{$file.dim.1} px</li>
                        {/if}

                            <li><b>{$CONST.IMAGE_SIZE}:</b> {$file.nice_size} KB</li>
                        </ul>
                    </div>
                </header>

                <input type="hidden" name="serendipity[mediaProperties][{$file@key}][image_id]" value="{$file.image_id}">

                <section class="media_file_props">
                    <h4>{$CONST.MEDIA_PROP}</h4>
                {foreach $file.base_property AS $prop_content}

                    <div class="form_{if $prop_content.type == 'textarea'}area{else}field{/if}">
                        <label for="mediaProperty{$prop_content@key}">{$prop_content.label}</label>
                    {if $prop_content.type == 'textarea'}

                        <textarea id="mediaProperty{$prop_content@key}" name="serendipity[mediaProperties][{$file@key}][{$prop_content.title}]" rows="5">{$prop_content.val|escape}</textarea>
                    {elseif $prop_content.type == 'readonly'}
                        {$prop_content.val|escape}
                    {elseif $prop_content.type == 'input'}

                        <input id="mediaProperty{$prop_content@key}" name="serendipity[mediaProperties][{$file@key}][{$prop_content.title}]" type="text" value="{$prop_content.val|escape}">
                    {/if}

                    </div>
                {/foreach}
                {if NOT $file.hotlink}

                    <div class="form_select">
                        <label for="newDir{$file@key}">{$CONST.FILTER_DIRECTORY}</label>
                        <input type="hidden" name="serendipity[oldDir][{$file@key}]" value="{$file.path|escape}">
                        <select id="newDir{$file@key}" name="serendipity[newDir][{$file@key}]">
                            <option{if empty($file.path)} selected="selected"{/if} value="">{$CONST.BASE_DIRECTORY}</option>
                        {foreach $media.paths AS $folder}

                            <option{if ($file.path == $folder.relpath)} selected="selected"{/if} value="{$folder.relpath}">{'&nbsp;'|str_repeat:($folder.depth*2)}{$folder.name}</option>{* * *}
                        {/foreach}

                        </select>
                    </div>
                {/if}

                </section>

                <section class="media_file_keywords">
                    <h4>{$CONST.MEDIA_KEYWORDS}</h4>

                    <ul class="clearfix plainList">
                    {foreach $file.base_keywords AS $keyword_cells}
                        {foreach $keyword_cells AS $keyword}
                        {if !empty($keyword.name)}

                        <li>
                            <input id="mediaKeyword{$keyword.name}{$file@key}" name="serendipity[mediaKeywords][{$file@key}][{$keyword.name}]" type="checkbox" value="true"{if $keyword.selected} checked="checked"{/if}>
                            <label for="mediaKeyword{$keyword.name}{$file@key}">{$keyword.name|truncate:20:"&hellip;"}</label>
                        </li>
                        {/if}
                        {/foreach}
                    {/foreach}

                    </ul>
                </section>

                <section class="media_file_metadata clearfix">
                    <h4>EXIF/IPTC/XMP</h4>
                {foreach $file.metadata AS $meta_data}

                    <h5>{$meta_data@key}</h5>
                    {if is_array($meta_data)}

                    <dl class="clearfix">
                        {foreach $meta_data AS $meta_value}

                        <dt>{$meta_value@key|escape}</dt>
                        <dd>{if is_array($meta_value)}{$meta_value|print_r}{else}{$meta_value|formatTime:DATE_FORMAT_SHORT:false:$meta_value@key|escape}{/if}</dd>
                        {/foreach}

                    </dl>
                    {else}

                    <p>{$meta_data|formatTime:DATE_FORMAT_SHORT:false:$meta_data@key}</p>
                    {/if}
                {/foreach}

                </section>
            {if $file.references}
                <section class="media_file_referer">

                    <h4>{$CONST.REFERER}</h4>

                    <ul>
                    {foreach $file.references AS $ref}

                        <li>({$ref.name|escape}) <a rel="nofollow" href="{$ref.link|escape}">{$ref.link|default:$CONST.NONE|escape}</a></li>
                    {/foreach}

                    </ul>
                </section>
            {/if}

            </article>
        {/if}
{/foreach}
