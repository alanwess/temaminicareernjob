(function($) {
    $(function() {
        var url = MyAutocomplete.url + "?action=hbgr_search";
        $( "#s" ).autocomplete({
            source: url,
            delay: 300,
            minLength: 3
        });
    });

    $(function() {
        var url = MyAutocomplete.url + "?action=hbgr_search_cat";
        $( "#localidade" ).autocomplete({
            source: url,
            delay: 300,
            minLength: 3
        });
    });

})(jQuery);