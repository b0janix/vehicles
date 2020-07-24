let $ = jQuery.noConflict();

$(function(){

    $("#filter-button").on('click', undefined, function() {
        let make_filter_call = false;
        let filter_obj = {};
        $('select').each(function() {
            if($(this).val().length > 0) {
                make_filter_call = true;
                filter_obj[$(this).attr('id')] = $(this).val();
            }
        });
        if (make_filter_call) {
            $.ajax({
                type: "POST",
                url: "/wp-json/vehicles/v1/filter",
                data: filter_obj,
                success: function (data) {
                    //data.includes();
                    if (data.length > 0) {
                        $(".vehicle").each(function () {
                            let id = $(this).attr('id');
                            if (!data.includes(id)) {
                                $(this).hide();
                            } else {
                                $(this).show();
                            }
                        })
                    } else {
                        $(".vehicle").hide();
                    }
                }
            });
        } else {
            $(".vehicle").show();
        }
    })

});