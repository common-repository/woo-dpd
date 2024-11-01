(function (window, document, $) {
    'use strict';

    var defaults = {

        templates: {
            base: ''
                + '<div class="DPD_pvz">'
                + ' <div class="DPD_title">'
                + '     <div class="DPD_cityPicker js-city-picker">'
                + '         <div>Ваш город:</div>'
                + '         <div class="DPD_cityLabel">'
                + '             <a href="javascript:void(0)" class="DPD_cityName js-city-current"></a>'
                + '             <div class="DPD_citySel">'
                + '                 <input class="DPD_citySearcher js-city-search" type="text" placeholder="Поиск города" autocomplete="off">'
                + '                 <div class="js-city-list"></div>'
                + '             </div>'
                + '         </div>'
                + '     </div>'
                + '     <div class="DPD_mark">'
                + '         <div class="DPD_courierInfo">'
                + '             <strong>Курьер: <span class="DPD_cPrice"><span class="js-tariff-courier-price"></span> руб.</span></strong>'
                + '             <br>'
                + '             <strong>Срок:<span class="DPD_cDate"><span class="js-tariff-courier-days"></span> дн.</span></strong>'
                + '         </div>'
                + '         <div style="clear: both"></div>'
                + '     </div>'
                + '     <div style="float:none; clear:both"></div>'
                + ' </div>'

                + ' <div class="DPD_map js-map"></div>'
                
                + ' <div class="DPD_info">'
                + '     <div class="DPD_sign">Пункты самовывоза</div>'
                + '     <div class="DPD_delivInfo_PVZ">'
                + '         <div>Стоимость <span class="DPD_pPrice"><span class="js-tariff-pickup-price"></span> руб.</span> срок <span class="DPD_pDate"><span class="js-tariff-pickup-days"></span> дн.</span></div>'
                + '     </div>'
                + '     <div class="DPD_modController js-terminal-type-picker">'
                + '         <div class="DPD_mC_block DPD_mC_ALL js-terminal-type-picker-item" data-type="all">Все</div>'
                + '         <div class="DPD_mC_block DPD_mC_PVZ js-terminal-type-picker-item" data-type="ПВП">Пункты выдачи</div>'
                + '         <div class="DPD_mC_block DPD_mC_POSTOMAT js-terminal-type-picker-item" data-type="П">Почтоматы</div>'
                + '     </div>'                
                + '     <a href="javascript:void(0);" class="DPD_arrow"></a>'
                + '     <div class="DPD_wrapper js-terminal-list"></div>'
                + '     <div id="DPD_ten"></div>'
                + ' </div>'
        
                + ' <div class="DPD_head">'
                + '     <div class="DPD_logo"><a href="http://ipolh.com" target="_blank"></a></div>'
                + ' </div>'
        
                + ' <div class="DPD_mask js-mask"></div>'
                + '</div>'
            ,

            city      : '<a class="DPD_citySelect"></a>',
            terminal  : '<p class="DPD_terminalSelect"></p>',
            placemark : {
                base: ''
                    + '<div class="DPD_baloon">'
                    + ' <div class="DPD_baloon_content">'
                    + '   <div class="DPD_iName">#NAME#</div>'
                    + '   <div class="DPD_iAdress">#ADDRESS_FULL#</div>'
                    + '   #SCHEDULES#'
                    + '   #DESCRIPTION#'
                    + ' </div>'
                    + ' <a href="javascript:void(0)" class="DPD_button js-terminal-btn-select" data-terminal-code="#CODE#"></a>'
                    + '</div>'
                ,

                schedule: ''
                    + '<div class="DPD_iSchedule">'
                    + ' <div><b>#HEAD#</b></div>'
                    + ' #ITEMS#'
                    + '</div>'
                ,

                scheduleItem: ''
                    + '<div>'
                    + ' <div class="DPD_iTime DPD_icon"></div>'
                    + ' <div class="DPD_baloonDiv">#TEXT#</div>'
                    + ' <div style="clear: both;"></div>'
                    + '</div>'
                ,

                description: ''
                    + '<div class="DPD_address-descr">'
                    + '  <div><b>Описание проезда</b></div>'
                    + '  <div>#TEXT#</div>'
                    + '</div>'
                ,
            }
        },

        placemarkIcon         : 'assets/js/dpd.widgets.map/assets/img/pickup_locationmarker.png',
        placemarkIconActive   : 'assets/js/dpd.widgets.map/assets/img/pickup_locationmarker_highlighted.png',
        placemarkIconPostomat : 'assets/js/dpd.widgets.map/assets/img/postamat--inactive.png',
        placemarkIconTerminal : 'assets/js/dpd.widgets.map/assets/img/terminal--inactive.png',
        placemarkIconSize     : [53, 53],
        placemarkIconOffset   : [-25, -53],
    }

    function DpdPickupMap(owner, options, data)
    {
        if (this === window) {
            return new DpdPickupMap(owner, options);
        }

        this.owner      = owner;
        this.options    = $.extend({}, defaults, options);
        this.cities     = [];
        this.terminals  = [];
        this.placemarks = [];

        this.init(data);
    }

    DpdPickupMap.prototype.init = function(data)
    {
        if (this.inited) {
            return true;
        }

        if (this.options.templates.base) {
            this.owner.innerHTML = this.options.templates.base;
        }

        this.showLoading();

        ymaps.ready(this._bind(function() {
            this.map = new ymaps.Map(this.owner.querySelector('.js-map'), {
                center  : [65, 81],
                zoom    : 5,
                controls: ['zoomControl']
            });

            this.map.controls.get('zoomControl').options.set('position', {
                left: this._zoomMargin(10).left, 
                top: 110
            });

            this._bindDomEvents();
            this._bindMapEvents();
            this.reload(data);
            this.hideLoading();
        }));
    }

    DpdPickupMap.prototype.reload = function(data)
    {
        this.setCities(data.cities || []);
        this.setTerminals(data.terminals || []);
        this.setTariff(data.tariffs.courier, 'courier');
        this.setTariff(data.tariffs.pickup, 'pickup');
    }

    DpdPickupMap.prototype.setCenter = function(x, y)
    {
        this.map.container.fitToViewport();

        if (x != window.undef && y != window.undef) {
            return this.map.setCenter([x, y]);
        }

        if (this.placemarks.length < 1) {
            return false;
        }

        if (this.placemarks.length > 1) {
            var bounds = this.map.geoObjects.getBounds();

            return this.map.setBounds(bounds, {
                zoomMargin: [this._zoomMargin().top, 0, 0, this._zoomMargin().left]
            });
        }

        return this.map.setCenter(this.placemarks[0].geometry.getCoordinates());
    }

    DpdPickupMap.prototype.setCities = function(items)
    {
        var $nodes = $('.js-city-picker', this.owner);

        this.cities = {};
        
        if (items.length <= 0) {
            $nodes.hide();
        } else {
            $('.js-city-list', $nodes).empty()
            
            for (var i = 0; i < items.length; i++) {
                var item  = this.cities[items[i].CODE] = items[i];
                var $node = $(this.options.templates.city)
                        .addClass('js-city-item')
                        .attr('data-city-code', item.CODE)
                        .html(item.NAME)
                    ;
                
                $('.js-city-list', $nodes).append($node);
                
                if (item.SELECTED) {
                    $('.js-city-current', this.owner).html(item.NAME);
                }
            }

            $nodes.show();
        }

        return this;
    }

    DpdPickupMap.prototype.setTariff = function(tariff, tariffType)
    {
        var node;

        ((node = this.owner.querySelector('.js-tariff-'+ tariffType +'-price'))
            && (node.innerHTML = '' + tariff.COST)
        );

        ((node = this.owner.querySelector('.js-tariff-'+ tariffType +'-days'))
            && (node.innerHTML = '' + tariff.DAYS)
        );

        return this;
    }

    DpdPickupMap.prototype.setTerminals = function(items)
    {
        this.clearTerminals();

        var types = ['all'];

        for (var i = 0; i < items.length; i++) {
            var item = items[i];

            var node = $(this.options.templates.terminal)
                    .addClass('js-terminal-list-item')
                    .attr('data-terminal-code', item.CODE)
                    .attr('data-terminal-type', item.TYPE)
                    .attr('data-terminal-addr', item.ADDRESS_FULL)
                    .html(item.NAME)

            var placemark = this._addPlacemark(item);

            if (!placemark) {
                continue;
            }

            $('.js-terminal-list', this.owner).append(node);
           
            this.placemarks.push(placemark);
            this.terminals[item.CODE] = item;

            if (items[i].TYPE && types.indexOf(items[i].TYPE) == -1) {
                types.push(items[i].TYPE);
            }
        }

        this._bindMapEvents();
        this.setTypes(types)
        this.setCenter();

        return this;
    }

    DpdPickupMap.prototype.clearTerminals = function()
    {
        this.map.geoObjects.removeAll();
        this.terminals  = [];
        this.placemarks = [];

        $('.js-terminal-list', this.owner).empty();
    }

    DpdPickupMap.prototype.setTypes = function(types)
    {
        if (types.length > 2) {
            $('.js-terminal-type-picker', this.owner).removeClass('dpd-hidden');
            $('.js-terminal-type-picker-item', this.owner).removeClass('dpd-hidden');
        } else {
            $('.js-terminal-type-picker', this.owner).addClass('dpd-hidden');
            $('.js-terminal-type-picker-item', this.owner).addClass('dpd-hidden');

            return;
        }

        $('.js-terminal-type-picker-item', this.owner).each(function() {
            if (types.indexOf(this.dataset.type) != -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        this.showByType('all');
    }

    DpdPickupMap.prototype.showByType = function(showedType)
    {
        var items = this.owner.querySelectorAll('.js-terminal-list-item:not(.dpd-hidden)');

        for (var i = 0; i < items.length; i++) {
            var isShow = showedType == 'all' || showedType == items[i].dataset.terminalType;

            $(items[i])[isShow ? 'show' : 'hide']();
            this.placemarks[i] && this.placemarks[i].options.set('visible', isShow);
        }

        $('.js-terminal-type-picker-item', this.owner)
            .removeClass('active')
            .filter('[data-type="'+ showedType +'"]')
                .addClass('active')
        ;
    }
    
    DpdPickupMap.prototype.showLoading = function()
    {
        this._loading && this.hideLoading();
        this._loading = true;

        $('.js-mask', this.owner).show();
    }

    DpdPickupMap.prototype.hideLoading = function()
    {
        this._loading = false;
        $('.js-mask', this.owner).hide();
    }

    DpdPickupMap.prototype.selectCity = function(code)
    {
        $(this.owner).trigger('dpd.map.city.select', [this.cities[code] || false, this]);
    }

    DpdPickupMap.prototype.selectTerminal = function(code)
    {
        $(this.owner).trigger('dpd.map.terminal.select', this.terminals[code] || false, this);
    }

    DpdPickupMap.prototype._bind = function(callback)
    {
        var self = this;

        callback = (typeof callback == 'function') 
            ? callback
            : this[callback]
        ; 

        return function () {
            return callback.apply(self, arguments);
        }
    }

    DpdPickupMap.prototype._zoomMargin = function(offset)
    {
        if (window.innerWidth > 919) {
            return {left: 260, top: 0};
        }

        return {left: 0 + (offset || 0), top: 100 + (offset || 0)};
    }

    DpdPickupMap.prototype._bindDomEvents = function()
    {
        $(this.owner).on('click', '.js-city-item',                 this._bind('onSelectCity'));
        $(this.owner).on('click', '.js-terminal-list-item',        this._bind('onClickTerminal'));
        $(this.owner).on('click', '.js-terminal-btn-select',       this._bind('onSelectTerminal'));
        $(this.owner).on('click', '.js-terminal-type-picker-item', this._bind('onSwitchShowedType'));
        $(this.owner).on('click', '.DPD_arrow',                    this._bind('onToggleList'));
        $(this.owner).on('keyup', '.js-city-search',               this._bind('onSearchCity'));
    }

    DpdPickupMap.prototype._bindMapEvents = function()
    {
        for (var i = 0; i < this.placemarks.length; i++) {
            this.placemarks[i] && this.placemarks[i].events.add('click', this._bind('onClickTerminal'));
        }
    }

    DpdPickupMap.prototype._addPlacemark = function(data)
    {
        if (!data.LAT || !data.LON) {
            return false;
        }

        var items = {
            'SELF_PICKUP'     : 'Прием посылок', 
            'SELF_DELIVERY'   : 'Выдача посылок', 
            'PAYMENT_CASH'    : 'Время приема наличных',
            'PAYMENT_CASHLESS': 'Время приема банковских карт',
        }

        var schedules = '';

        for (var i in items) {
            var timetable = data['SCHEDULE_'+ i];
            
            if (timetable.length <=0) {
                continue;
            }

            var text = '';

            for (var j in timetable) {
                text += this.options.templates.placemark.scheduleItem.replace('#TEXT#', timetable[j]);
            }

            schedules += this.options.templates.placemark.schedule
                .replace('#HEAD#', items[i])
                .replace('#ITEMS#', text)
            ;
        }

        var balloon = this.options.templates.placemark.base
                .replace('#NAME#', data.NAME)
                .replace('#ADDRESS_FULL#', data.ADDRESS_FULL)
                .replace('#SCHEDULES#', schedules)
                .replace('#DESCRIPTION#', data.ADDRESS_DESCR ? this.options.templates.placemark.description.replace('#TEXT#', data.ADDRESS_DESCR) : '')
                .replace('#CODE#', data.CODE)
            ;

        var icon = this.options.placemarkIcon;

        if (data.TYPE == 'П' && this.options.placemarkIconPostomat) {
			icon = this.options.placemarkIconPostomat;
		} else if (data.TYPE == 'ПВП' && this.options.placemarkIconTerminal) {
            icon = this.options.placemarkIconTerminal;
		}

		var placemark = new ymaps.Placemark(
			[data.LAT, data.LON],
            
            {
                hintContent: data.NAME,
                balloonContent: balloon,
            },

			{
				balloonCloseButton: true,
				iconLayout: 'default#image',
                iconImageHref: icon,
				iconImageSize: this.options.placemarkIconSize,
				iconImageOffset: this.options.placemarkIconOffset
			}
		);

		this.map.geoObjects.add(placemark);

		return placemark;
    }

    DpdPickupMap.prototype._highlightTerminal = function(parms)
    {
        var $nodes = $('.js-terminal-list-item', this.owner);
        var $node  = parms.code 
                    ? $nodes.filter('[data-terminal-code="'+ parms.code +'"]')
                    : $nodes.filter(':eq('+ parms.index +')');

        if (!$node.length) {
            return false;
        }

        $nodes.removeClass('DPD_chosen');
        $node.addClass('DPD_chosen');

        var index = parms.index || $nodes.index($node);

        if (!this.placemarks[index].balloon.isOpen()
            && (!!parms.openBalloon) !== false
        ) {
            this.placemarks[index].balloon.open();
        }

        if (!!parms.highlightIcon) {	
            this.placemarks[index].options.set('iconImageHref', this.options.placemarkIconActive);
        }
    }

    DpdPickupMap.prototype.onSelectCity = function(e) 
    {
        var code = e.target.dataset.cityCode;

        this.selectCity(code);

        return false;
    }

    DpdPickupMap.prototype.onClickTerminal = function(e)
    {
        var parms = {openBalloon: !!e.target};

        if (e.target) {
            parms.code  = e.target.dataset.terminalCode;
        } else {
            parms.index = this.placemarks.indexOf(e.get('target'));
        }

        this._highlightTerminal(parms);

        $('.DPD_arrow', this.owner).removeClass('up');
        $('.js-terminal-list', this.owner).removeClass('show');

        return false;
    }

    DpdPickupMap.prototype.onSelectTerminal = function(e)
    {
        var code = e.target.dataset.terminalCode;

        this.selectTerminal(code);
    }

    DpdPickupMap.prototype.onSearchCity = function(e) 
    {
        var text  = $.trim(e.target.value.toLowerCase());
        
        $('.js-city-item', this.owner).each(function() {
            if (this.textContent.toLowerCase().indexOf(text) != -1) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    }

    DpdPickupMap.prototype.onSwitchShowedType = function(e)
    {
        var showedType = e.target.dataset.type;

        this.showByType(showedType);

        return false;
    }

    DpdPickupMap.prototype.onToggleList = function(e) {
        var arrow = this.containerNode.querySelectorAll('.DPD_arrow');
        var list  = this.containerNode.querySelectorAll('#DPD_wrapper');

        [].forEach.call(arrow, function(node) {
            BX.toggleClass(node, ['up', 'down']);
        });

        [].forEach.call(list, function(node) {
            BX.toggleClass(node, ['show', 'hide']);
        });

        return false;
    }

    $.fn.dpdMap = function (parms, data) {
        return this.each(function () {
            var $this    = $(this);
            var instance = $this.data('dpd.map');

            if (!instance) {
                instance = new DpdPickupMap(this, parms, data);
                $this.data('dpd.map', instance);

                return $this;
            }

            var method = arguments[0];
            var args   = [].slice.call(arguments).slice(1);

            return instance.apply(method, args);
        });
    }
})(window, document, jQuery);