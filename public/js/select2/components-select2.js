var ComponentsSelect2 = function() {

    var handleDemo = function() {

        // Set the "bootstrap" theme as the default theme for all Select2
        // widgets.
        //
        // @see https://github.com/select2/select2/issues/2927
        $.fn.select2.defaults.set("theme", "bootstrap");

        // @see https://select2.github.io/examples.html#data-ajax
        function formatRepo(repo) {
            if (repo.loading) return repo.text;

            var markup = "<div class='select2-result-repository clearfix'>" +
                "<div class='select2-result-repository__meta'>" +
                "<div class='select2-result-repository__title'>" + repo.username + "</div></div></div>";

            return markup;
        }

        function formatRepoSelection(repo) {
            return repo.username;
        }
        
        var csr_token = $('meta[name="csrf-token"]').attr('content')

        $(".autocomplete-user").each(function(){
            var $this = $(this);
            var autourl = $this.find(".autocomplete-user").data('ajax-url');
            $this.select2({
                width: "off",
                ajax: {
                    type: "POST",
                    url: autourl,
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            _token: csr_token,
                            username: params.term, // search term
                            page: params.page
                        };
                    },
                    processResults: function(data, page) {
                        // console.log(data);
                        // parse the results into the format expected by Select2.
                        // since we are using custom formatting functions we do not need to
                        // alter the remote JSON data
                        return {
                            results: data
                        };
                    },
                    cache: true
                },
                escapeMarkup: function(markup) {
                    return markup;
                }, // let our custom formatter work
                minimumInputLength: 1,
                templateResult: formatRepo,
                templateSelection: formatRepoSelection
            });
        });
    }

    return {
        //main function to initiate the module
        init: function() {
            handleDemo();
        }
    };

}();

jQuery(document).ready(function() {
    ComponentsSelect2.init();
});
