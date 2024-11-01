(function($){
    $(document).ready(function(){

        $('.datepicker').datepicker();

        $('.dpd-sender-terminal').each(function() {
            var cityId   = $(this).data('city');
            var terminal = $(this).data('value');

            if (cityId && terminal) {
                loadTerminalSelector($(this), cityId, terminal);
            }
        })

        $(document).on('click', '.dpd-tab', function(e){
            e.preventDefault();
            var self = $(this);
            var tabsContentLevel = self.closest('nav').data('tabs-content-level');
            self.parent().find('.dpd-tab').removeClass('nav-tab-active');
            self.addClass('nav-tab-active');

            $('.dpd-tab-content-' + tabsContentLevel).hide();
            $('#' + self.data('tab-content-id')).show();
        });

        $('#dpd_status_order_check').change(function(){
            var checked = $(this).is(':checked');
            $('.dpd-select.status').each(function(){
                if (checked) {
                    $(this).removeAttr('disabled');
                } else {
                    $(this).attr('disabled', '');
                }
            });
        });

        $(document).on('click', '.dpd-sender-city, .dpd-city-autocomplete', function() {
            var $input    = $(this);
            var $hidden   = $('#'+ this.id +'_id');
            var $terminal = $('#'+ this.id.replace('_city', '') + '_terminal_code');

            
            if ($input.data('dpd-autocomplete')) {
                return ;
            }

            $input.data('dpd-autocomplete', true);

            $input.autoComplete({
                minLength: 3,
                source: function(term, response){
                    try { xhr.abort(); } catch(e){}
                    xhr = $.getJSON(location.href + '&action=getCity', { q: term }, function(data){
                        response(data); 
                    });
                },
                renderItem: function (item, search){
                    var re = new RegExp('('+search+')', "ig");
                    return '<div class="autocomplete-suggestion" data-city-id="'+item[1]+
                        '" data-val="'+search+'" data-city="'+item[0]+'">'+item[0].replace(re, "<b>$1</b>")+'</div>';
                },
                onSelect: function(e, term, item) {
                    var city = item.data('city');
                    var cityId = item.data('city-id');

                    $input.val(city);
                    $hidden.val(cityId);

                    loadTerminalSelector($terminal, cityId, 0);

                    $input.removeClass('dpd-no-ajax-update').trigger('change');
                },
                delay: 50,
            });
        })

        $(document).on('change', '#dpd_order input, #dpd_order select', function(e){
            var self = $(this);

            if (self.hasClass('dpd-no-ajax-update')) {
                return ;
            }

            var form = self.closest('form');
            var formData = form.serialize();
            
            $('#dpd_order .send-order').attr('disabled', '');
            proccessOrderForm(
                '?action=orderBlock&order_id=' + $('#dpd_order input[name="order[id]"]').val() +
                    '&dpd_active_tab=' + $('.order-content .nav-tab.dpd-tab.nav-tab-active').
                    data('tab-content-id'),
                formData,
                'post',
                null,
                function(html){
                    $('#dpd_order .send-order').removeAttr('disabled');
                    $(document).find('#dpd_order .order-content').html(
                       $(html).find('.order-content').html() 
                    );
            });
        });

        $(document).on('click', '#dpd_order .send-order', function(e){
            e.preventDefault();
            var self = $(this);
            var form = self.closest('form');
            var formData = form.serialize();
            self.attr('disabled', '');
            proccessOrderForm('?action=sendOrder', formData, 'post', 'json', function(response){
                self.removeAttr('disabled');
                if (response.data) {
                    self
                        .hide()
                        .removeAttr('disabled')
                    ;

                    form.find('input,select').each(function(){
                        if (!$(this).hasClass('dpd-no-ajax-update')) {
                            $(this).attr('disabled', 'disabled');
                        }
                    });

                    $('#dpd_order .cancel-order').removeAttr('disabled').show();
                    $('#dpd_order .cancel-button').removeAttr('disabled');
                    $('#dpd_id').text(response.data.id);
                    $('#dpd_status').text(response.data.status);
                    $('#dpd-docs-form').show();
                    $('#dpd-docs-error').hide();
                }
            });
        });

        $(document).on('click', '#dpd_order .cancel-order', function(e){
            e.preventDefault();
            var self = $(this);
            var form = $('#dpd_order');
            
            self.attr('disabled', '');

            proccessOrderForm(
                '?action=CancelOrder&order_id=' + 
                    $('#dpd_order input[name="order[id]"]').val(),
                {},
                'get',
                'json',
                function(response){
                    self
                        .hide()
                        .removeAttr('disabled')
                    ;

                    form.find('input,select').each(function(){
                        if (!$(this).hasClass('dpd-no-ajax-update')) {
                            $(this).removeAttr('disabled');
                        }
                    });

                    $('#dpd_order .cancel-order').hide();
                    $('#dpd_order .send-order').show();
                    $('#dpd_id').text(response.data.id);
                    $('#dpd_status').text(response.data.status);
                    $('#dpd-docs-form').hide();
                    $('#dpd-docs-error').show();
            });
        });

        $(document).on('click', '#dpd_order #download_invoice_file', function(e){
            e.preventDefault();
            var self = $(this);
            printDocs('?action=printDocs&type=invoice&order_id=' +
                $('#dpd_order input[name="order[id]"]').val(), self);            
        });

        $(document).on('click', '#dpd_order #download_label_file', function(e){
            e.preventDefault();
            var self = $(this);
            printDocs(
                '?action=printDocs&type=label&order_id=' +
                    $('#dpd_order input[name="order[id]"]').val() + 
                    '&label_count=' + $('#dpd_order #dpd_label_count').val() +
                    '&file_format=' + $('#dpd_order #dpd_file_format').val() +
                    '&print_area_format=' + $('#dpd_order #dpd_print_area_format').val(),
                self
            );            
        });
    });
})(jQuery);


function printDocs(url, button)
{
    button.attr('disabled', '');
    jQuery.getJSON(url, function(response) {
        console.log(response)
            button.removeAttr('disabled');

            if (response.error) {
                jQuery('.notifications').html(
                    '<div class="notice notice-error inline">' + 
                    response.error + '</div>'
                );  
            } else {
                if (button.next('a').length > 0) {
                    button.next('a').attr('href', response.file);
                } else {
                    jQuery('<a />')
                        .attr('href', response.file)
                        .attr('target', '_blank')
                        .attr('style', 'display: inline-block; margin-left: 20px')
                        .html('скачать файл')
                        .insertAfter(button)
                    ;
                }
            }
    });
}

function proccessOrderForm(url, data, type, dataType, callback)
{
    var orderForm = jQuery('#dpd_order');
    jQuery.ajax({
        url: url,
        data: data,
        type: type,
        dataType: dataType,
        beforeSend: function(){
            jQuery('.notifications').html('');
            orderForm.find('input,select').each(function(){
                jQuery(this).attr('disabled', '');
            });
            jQuery('#dpd_order .cancel-button').attr('disabled', '');
        },
        success: function(response){
            jQuery('#dpd_order .cancel-button').removeAttr('disabled');
            jQuery('#dpd_order .order-content').animate({ scrollTop: 0 }, "fast");
            
            orderForm.find('input,select').each(function(){
                jQuery(this).removeAttr('disabled');
            });

            if (response.error) {
                jQuery('.notifications').html(
                    '<div class="notice notice-error inline">' + 
                    response.error + '</div>'
                );                        
            } else {
                jQuery('.notifications').html(
                    '<div class="notice notice-success inline">' + 
                    response.success + '</div>'
                );
            }
            jQuery('#dpd_order .cancel-button').removeAttr('disabled');
            callback(response);
        }
    });
}


function loadTerminalSelector(selector, cityId, value, disabled) {
    jQuery.ajax({
        url: location.href + '&action=getTerminalsByCityId&city_id=' + cityId,
        dataType: 'json',
        beforeSend: function() {
            selector.html('').attr('disabled', '');
        },
        success: function(response) {
            selector.html(response.html)
                .removeAttr('disabled');
            if (value) {
                selector.val(value);
            }
            if (disabled) {
                selector.attr('disabled', '');
            }
        },
        error: function() {
            alert('Connection problem. Please, try later.');
        }
    });
}

function runImportData(step, offset) {
    jQuery.ajax({
        url: location.href + '&action=import&offset=' + offset + '&step=' + step,
        dataType: 'json',
        success: function(response) {
            if (response.error) {
                jQuery('#progress_message').removeClass('notice-info');
                jQuery('#progress_message').addClass('notice-error');
                jQuery('#progress_message p').text(response.error);
                setTimeout(function(){
                    location.href = location.href + '&action=importDone';
                }, 2000);
            } else {
                jQuery('.progress-bar').attr('style', 'width:' + response.percent + '%');
                jQuery('.progress-bar span').text(response.percent + '%');
                jQuery('#progress_message p #step').text(Number(response.step) + 1);
                jQuery('#progress_message p #stepname').text(response.stepname);
                if (response.step == -1){
                    jQuery('.progress-bar-wrapper').attr('style', 'display:none');
                    jQuery('#progress_message').removeClass('notice-info');
                    jQuery('#progress_message').addClass('notice-success');
                    jQuery('#progress_message p').text(response.stepname);
                    setTimeout(function(){
                        location.href = location.href + '&action=importDone';
                    }, 2000);
                } else {
                    runImportData(response.step, response.offset);
                }
            }
        },
        error: function() {
            alert('Connection problem. Let\'s try again.');
            runImportData(step, offset);
        }
    });
}