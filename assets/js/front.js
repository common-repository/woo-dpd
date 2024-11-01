(function($){
    $(document).ready(function(){

        $('form.checkout').on('change', '#payment input[type="radio"]', function(){
            $('input[name="dpd_terminal_code"]').val('');
            $(document.body).trigger('update_checkout');
        });

        $('#billing_city, #calc_shipping_city, #shipping_city').autoComplete({
            minLength: 3,
            source: function(term, response){
                try { xhr.abort(); } catch(e){}
                if ($('#billing_city').is(':focus')) {
                    var country = $('#billing_country').val();
                }
                if ($('#calc_shipping_city').is(':focus')) {
                    var country = $('#calc_shipping_country').val();
                }
                if ($('#shipping_city').is(':focus')) {
                    var country = $('#shipping_country').val();
                }
                xhr = $.getJSON('?&action=getCity&country=' + country, { q: term }, function(data){
                    response(data); 
                });
            },
            renderItem: function (item, search){
                var re = new RegExp('('+search+')', "ig");
                return '<div class="autocomplete-suggestion" data-val="'+search+'" data-city="'+item[0]+'">'+item[0].replace(re, "<b>$1</b>")+'</div>';
            },
            onSelect: function(e, term, item){
                var result    = item.data('city').split(', ');
                var subregion = result[ result.length - 3 ];
                var region    = result[ result.length - 2 ];
                var city      = result[ result.length - 1 ];

                if ($('#billing_city').is(':focus')) {
                    $('#billing_city').val(city);
                    $('#billing_state').val((subregion ? subregion +', ' : '') + region);
                    $('#billing_postcode').val('');
                }

                if ($('#calc_shipping_city').is(':focus')) {
                    $('#calc_shipping_city').val(city);
                    $('#calc_shipping_state').val((subregion ? subregion +', ' : '') + region);
                }

                if ($('#shipping_city').is(':focus')) {
                    $('#shipping_city').val(city);
                    $('#shipping_state').val((subregion ? subregion +', ' : '') + region);
                    $('#shipping_postcode').val('');
                }

                $(document.body).trigger('update_checkout');
            },
            delay: 50,
        });

        $(document.body).on('updated_checkout', function(event){
            $('#select_dpd_pickpoint').remove();
            $('.shipping_method').each(function(){
                var shippingMethod = $(this);
                var shippingMethodVal = shippingMethod.val();
                if (shippingMethodVal.indexOf('dpd') !== -1) {
                    if (shippingMethod.prop('checked')) {
                        var valueParts = shippingMethodVal.split('_');
                        if (valueParts[2] == 'pickup') {
                            shippingMethod.closest('li').append('<br><a href="javascript:void(0);"' +
                                'id="select_dpd_pickpoint">' +
                                DPD_SELECT_PICKPOINT_TEXT + '</a>'
                            );
                            var country = $('#billing_country').val();
                            var region = $('#billing_state').val();
                            var city = $('#billing_city').val();
                            var paymentMethod = $('input[name="payment_method"]:checked').val();
                            if ($('#ship-to-different-address-checkbox').is(':checked')) {
                                country = $('#shipping_country').val();
                                region = $('#shipping_state').val();
                                city = $('#shipping_city').val();
                            }
                            if (country && region && city) {
                                $('.dpd-overlay').remove();
                                $('.dpd-popup').remove();
                                new DpdPickPointWidget(
                                    '#select_dpd_pickpoint',
                                    country,
                                    region,
                                    city,
                                    paymentMethod,
                                    function (response) {
                                        $('input[name="dpd_terminal_code"]').val(response.CODE);
                                        $('#dpd_pickpont_descr').remove();
                                        $('#billing_address_1').val(response.NAME + '(' + response.CODE + ')');
                                        $('#select_dpd_pickpoint')
                                        .before('<p style="margin: 0;" id="dpd_pickpont_descr">' +
                                            response.NAME
                                            + '</p>');
                                    }
                                );
                            }
                            return;
                        }
                    }
                }
            });

            var val = $(document.body).find('.shipping_method:checked').val() || $(document.body).find('.shipping_method:first').val();
            setDeliveryData(val);
        });

        $(document.body).on('change', '.shipping_method', function(){
            var val = $(this).val();
            setDeliveryData(val);
        });
    });

    function setDeliveryData(val)
    {
        if (val !== undefined && val.indexOf('dpd_') !== -1) {
            var valueParts = val.split('_');
            if (valueParts[2] == 'pickup') {
                $('input[name="dpd_delivery_type"]').val('pickup');
            } else {
                $('input[name="dpd_delivery_type"]').val('courier');
            }
            $('input[name="dpd_data"]').val(val);
        } else {
            $('input[name="dpd_data"]').val('');
            $('input[name="dpd_delivery_type"]').val('');
            $('input[name="dpd_terminal_code"]').val('');
        }
    }
})(jQuery);