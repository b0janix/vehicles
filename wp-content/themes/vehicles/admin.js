let $ = jQuery.noConflict();

$(function(){
    let years = [];
    $("#add_year_btn").on('click', undefined, function() {
        let year_input = $("#add_year_input");
        let year_val = parseInt(year_input.val());
        let current_year = new Date().getFullYear();
        if (Number.isNaN(year_val) || year_val < 1886 || year_val > current_year) {
            alert("Invalid year input");
            return;
        }
        if(!years.includes(year_val)) {
            $("#year_dropdown").append(`<option value="${year_val}">${year_val}</option>`);
            $("#year_dropdown").val(year_val);
            years.push(year_val);
        }
        year_input.val("");
    });

    let generateOptions = function(type, low, high) {
        $(`#add_${type}_btn`).on('click', undefined, function() {
            let input = $(`#add_${type}_input`);
            let input_val = input.val();
            if(input_val.length < low || input_val.length > high) {
                alert("Invalid input");
                return;
            }
            $(`#${type}_dropdown`).append(`<option value="${input_val}">${input_val}</option>`);
            $(`#${type}_dropdown`).val(input_val);
            years.push(input_val);
            input.val("");
        });
    }

    generateOptions('make', 2, 50);
    generateOptions('model', 2, 100);
    generateOptions('sku', 8, 12);
    generateOptions('location', 2, 200);

    $("#price_value").on('change', undefined, function() {
        let price_val = parseInt($(this).val());
        //500 is the min amount
        if(Number.isNaN(price_val) || price_val < 500 || price_val > 10000000) {
            alert("Invalid price input");
            $(this).val(500);
        }
    });

    let preventIt = true;

    $("#submit").on('click', undefined, function(e) {
        let submitButton = $(this);
        if(preventIt) {
            e.preventDefault();
            if($(".checkable:checked").length < 3) {
                alert("Please check the required checkboxes from the Vehicle Details section");
                return;
            }

            let counter = 0;
            $(".selectable").each(function() {
                if($(this).val()) {
                    counter++;
                }
            });
            if(counter < 5) {
                alert("Please add values to all dropdowns from the Vehicle Meta section");
                return;
            }
            if(!$("#price_value").val()) {
                alert("Please add price value");
                return;
            }

            let price_value = parseInt($("#price_value").val());
            if(Number.isNaN(price_value) || price_value < 500 || price_value > 10000000) {
                alert("Invalid price input");
                $(this).val(500);//500 is the min amount
                return;
            }

            price_value = $("#price_value").val();
            let price_currency = $("#price_currency").val();
            let price_type = $("#price_type").val();
            let sold = $('.checkable_sold:checked').attr('id') === 'sold_yes' ? 1 : 0;
            let arrive = $('.checkable_arrive:checked').attr('id') === 'arrive_yes' ? 1 : 0;
            let available = $('.checkable_available:checked').attr('id') === 'available_yes' ? 1 : 0;
            let year = $('#year_dropdown').val();
            let make = $('#make_dropdown').val();
            let model = $('#model_dropdown').val();
            let sku = $('#sku_dropdown').val();
            let location = $('#location_dropdown').val();
            let description = $('#description').val();
            let image_1 = $('#image_1').val();
            let image_2 = $('#image_2').val();

            $.ajax({
                type: "POST",
                url: "/wp-json/vehicles/v1/insert",
                data: {
                    sold, arrive, available, year, make, model, sku, location, description, price_value, price_type, price_currency, image_1, image_2
                },
                success: function (data) {
                    if (data.status) {
                        preventIt = false;
                        submitButton.click();
                    }
                    else {

                        alert(data.message);
                    }
                }
            });
        }
    });

    let toggleCheckboxes = function(name) {
        $(`.checkable_${name}`).on('click', undefined, function() {
            let checked = $(this);
            $(`.checkable_${name}`).each(function() {
                if(checked.attr('name') !== $(this).attr('name')) {
                    $(this).attr('checked', false);
                }
            })
        });
    }

    toggleCheckboxes('sold');
    toggleCheckboxes('arrive');
    toggleCheckboxes('available');

});