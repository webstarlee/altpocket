//== Class definition
var Select2 = function() {
    //== Private functions
    var demos = function() {
        // basic
        $('#m_select2_1, #m_select2_1_validate').select2({
            placeholder: "Select a state"
        });
        $('#m_select2_1_2, #m_select2_1_2_validate').select2({
            placeholder: "Select a state"
        });

        // nested
        $('#m_select2_2, #m_select2_2_validate').select2({
            placeholder: "Select a state"
        });

        // multi select
        $('#m_select2_3, #m_select2_3_validate').select2({
            placeholder: "Select a state",
        });

        // basic
        $('#m_select2_4').select2({
            placeholder: "Select a state",
            allowClear: true
        });

        // loading data from array
        var data = [{
            id: 0,
            text: 'Enhancement'
        }, {
            id: 1,
            text: 'Bug'
        }, {
            id: 2,
            text: 'Duplicate'
        }, {
            id: 3,
            text: 'Invalid'
        }, {
            id: 4,
            text: 'Wontfix'
        }];

        $('#m_select2_5').select2({
            placeholder: "Select a value",
            data: data
        });

        // loading remote data

        function formatRepo (repo) {
        	if (repo.loading) return repo.name;

        	var markup = "<div class='select2-result-repository clearfix' style='font-size:12px;'>" +
        		"<div class='select2-result-repository__avatar'><img style='width:24px;' src='https://altpocket.io/icons/32x32/" + repo.symbol + ".png' /> " + repo.name + " (" + repo.symbol + ")</div>";



        	return markup;
        }

        function formatRepoSelection (repo) {
        	return repo.name || repo.text;
        }

        $("#m_select2_6").select2({
            placeholder: "Find a token",
            allowClear: true,
            ajax: {
                url: "/api/coins2/",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                      results: data.tokens,
                      pagination: {
                        more: (params.page * 30) < data.total_count
                      }
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepo, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });


        $(".m_token_select").select2({
            placeholder: "Find a token",
            allowClear: true,
            ajax: {
                url: "/api/coins2/",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                      results: data.tokens,
                      pagination: {
                        more: (params.page * 30) < data.total_count
                      }
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepo, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });

        $("#m_select2_6_2").select2({
            placeholder: "Find a token",
            allowClear: true,
            ajax: {
                url: "/api/coins2/",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function(data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                      results: data.tokens,
                      pagination: {
                        more: (params.page * 30) < data.total_count
                      }
                    };
                },
                cache: true
            },
            escapeMarkup: function(markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepo, // omitted for brevity, see the source of this page
            templateSelection: formatRepoSelection // omitted for brevity, see the source of this page
        });

        // custom styles

        // tagging support
        $('#m_select2_12_1, #m_select2_12_2, #m_select2_12_3, #m_select2_12_4').select2({
            placeholder: "Select an option",
        });

        // disabled mode
        $('#m_select2_7').select2({
            placeholder: "Select an option"
        });

        // disabled results
        $('#m_select2_8').select2({
            placeholder: "Select an option"
        });

        // limiting the number of selections
        $('#m_select2_9').select2({
            placeholder: "Select an option",
            maximumSelectionLength: 2
        });

        // hiding the search box
        $('#m_select2_10').select2({
            placeholder: "Select an option",
            minimumResultsForSearch: Infinity
        });

        $('#m_select2_10_2').select2({
            placeholder: "Select an option",
            minimumResultsForSearch: Infinity
        });

        // tagging support
        $('#m_select2_11').select2({
            placeholder: "Add a tag",
            tags: true
        });
    }

    var modalDemos = function() {
        $('#m_select2_modal').on('shown.bs.modal', function () {
            // basic
            $('#m_select2_1_modal').select2({
                placeholder: "Select a state"
            });

            // nested
            $('#m_select2_2_modal').select2({
                placeholder: "Select a state"
            });

            // multi select
            $('#m_select2_3_modal').select2({
                placeholder: "Select a state",
            });

            // basic
            $('#m_select2_4_modal').select2({
                placeholder: "Select a state",
                allowClear: true
            });
        });
    }

    //== Public functions
    return {
        init: function() {
            demos();
            modalDemos();
        }
    };
}();

//== Initialization
jQuery(document).ready(function() {
    Select2.init();
});
