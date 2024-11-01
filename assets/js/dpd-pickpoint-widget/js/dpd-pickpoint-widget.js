var DpdPickPointWidget = (function(){
    function DpdPickPointWidget(selector, country, region, city, paymentMethod, callback) {
        this.selector = selector;
        this.country = country;
        this.region = region;
        this.city = city;
        this.paymentMethod = paymentMethod;
        this.callback = callback;
        this.init();
    }

    //инициализация виджета
    DpdPickPointWidget.prototype.init = function(){

        var self = this;

        //обработка события нажатия на ссылку "Выбрать ПВЗ"
        jQuery(document.body).find(this.selector).click((function(e){
            
            e.preventDefault();
            
            if (!jQuery(document.body).find('.dpd-overlay').length) {
                
                //добавляем оверлей и попап
                jQuery(document.body).append(
                    '<div class="dpd-overlay"></div>'+
                    '<div class="dpd-popup">'+
                    '<iframe id="dpd_iframe" src="'+ DPD_HOME_URI +'?action=getMap&'+
                    'country=' + self.country + '&region=' + self.region +
                    '&city=' + self.city + '&payment_method=' + self.paymentMethod + '"></iframe>'+
                    '<img class="loader" src="' + DPD_PLUGIN_URI + 'assets/js/dpd-pickpoint-widget/'+
                    'img/ajax-loader.gif">'+
                    '</div>'
                );

                jQuery('#dpd_iframe').show().on('load', function(){
                    jQuery('.dpd-popup .loader').remove();
                    jQuery(this).show();
                });

                jQuery(window).on("message", function(e) {
                    var response = e.originalEvent.data;
                    if (response.type != 'close') {
                        self.callback(response.data);
                    }
                    self.close();
                });


                jQuery(document).on("click", ".dpd-overlay", function(e) {
                    self.close();
                });

            } else {
                //если оверлей есть, значит карту и попап нужно только показать
                jQuery(document.body).find('.dpd-overlay').show();
                jQuery(document.body).find('.dpd-popup').show();
            }
        }));
    };

    //метод скрытия попапа и оверлея
    DpdPickPointWidget.prototype.close = function() {
        jQuery('.dpd-popup').fadeOut();
        jQuery('.dpd-overlay').fadeOut();
    };

    return DpdPickPointWidget;
})();