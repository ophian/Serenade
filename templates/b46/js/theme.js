function checkWebP(callback) {
    var webP = new Image();
    webP.onload = webP.onerror = function () {
        callback(webP.height == 2);
    };
    webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
};

checkWebP(function(support) {
    if (!support) {
        $('a.serendipity_image_link').each(function() {
            var $currentA = $(this);
            var  dataHref = $currentA.attr('data-fallback');
            $currentA.attr('href', dataHref);
        });
    }
});

(function($) {
    $('#serendipity_replyTo').addClass('form-control');
    $('<div id="LSResult" style="display: none;"><div id="LSShadow"></div></div>').appendTo('#searchform');
    $('.serendipity_entrypaging_left a').addClass('btn btn-secondary');
    $('.serendipity_entrypaging_right a').addClass('btn btn-secondary');
    $('.serendipity_edit_nugget').attr('class', 'bi bi-pencil-square text-editicon serendipity_edit_nugget btn btn-admin btn-sm');
    $('.msg_notice.serendipity_subscription_off').attr('class', 'alert alert-warning serendipity_subscription_off').attr('role', 'alert');
    $('.serendipity_msg_important.msg_error').attr('class', 'alert alert-danger jq').attr('role', 'alert');
    $('.serendipity_msg_important').addClass('alert alert-secondary jq').attr('role', 'alert');
    $('.serendipity_msg_success').addClass('alert alert-success jq').attr('role', 'alert');
    $('.serendipity_msg_notice').addClass('alert alert-info jq').attr('role', 'alert');
    $('#category_submit').addClass('btn btn-outline-secondary btn-sm');
    $('.comments-enable').attr('class', 'btn btn-secondary btn-sm btn-admin comments-enable');
    $('.serendipity_freeTag_xmlTagEntry > .serendipity_xml_icon > img.serendipity_freeTag_xmlButton').replaceWith('<svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-rss-fill" role="img" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-labelledby="title"><title id="title">XML</title><path fill-rule="evenodd" d="M2 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H2zm1.5 2.5a1 1 0 0 0 0 2 8 8 0 0 1 8 8 1 1 0 1 0 2 0c0-5.523-4.477-10-10-10zm0 4a1 1 0 0 0 0 2 4 4 0 0 1 4 4 1 1 0 1 0 2 0 6 6 0 0 0-6-6zm.5 7a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3z"></path></svg>');
    $('.trackback details > div > a').attr('class', 'btn btn-secondary btn-sm btn-admin trackbacks-delete');
    $('#trackback_url').next('.alert-trackback').hide();
    $('#trackback_url').click(function(e) { e.preventDefault(); $(this).next('.alert-trackback').show(); });
    $('.serendipity_entrypaging').addClass('mobile');
})(jQuery);