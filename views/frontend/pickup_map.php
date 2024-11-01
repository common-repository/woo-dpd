<!DOCTYPE>
<html>
<head>
    <meta charset="UTF-8">
    <title>DPD pickup map widget</title>

    <link rel="stylesheet" type="text/css" href="<?php echo DPD_PLUGIN_URI; ?>assets/js/dpd.widgets.map/src/css/style.css">
    <link rel="stylesheet" type="text/css" href="<?php echo DPD_PLUGIN_URI; ?>assets/js/dpd-pickpoint-widget/css/dpd-pickpoint-widget.css?v5">
    <link rel="stylesheet" type="text/css" href="<?php echo DPD_PLUGIN_URI; ?>assets/js/dpd-pickpoint-widget/css/iframe.css">

    <script src="https://api-maps.yandex.ru/2.1/?lang=ru_RU"></script>
    <script src="/wp-includes/js/jquery/jquery.min.js"></script>
    <script src="<?php echo DPD_PLUGIN_URI; ?>assets/js/dpd.widgets.map/src/js/jquery.dpd.map.js?v2"></script>
    <script src="<?php echo DPD_PLUGIN_URI; ?>assets/js/dpd-pickpoint-widget/js/dpd-pickpoint-widget.js?v5"></script>

    <script>
        (function($) {

            $(function() {
                'use strict';

                setTimeout(function(){
                    var $r = $('#dpd-map').dpdMap(
                        {
                            // placemarkIcon : '<?php echo DPD_PLUGIN_URI; ?>assets/js/dpd.widgets.map/assets/img/pickup_locationmarker.png',
                            // placemarkIconActive : '<?php echo DPD_PLUGIN_URI; ?>assets/js/dpd.widgets.map/assets/img/pickup_locationmarker_highlighted.png',

                            placemarkIcon         : '<?php echo DPD_PLUGIN_URI; ?>assets/js/dpd.widgets.map/assets/img/pickup_locationmarker.png',
                            placemarkIconPostomat : '<?php echo DPD_PLUGIN_URI; ?>assets/js/dpd.widgets.map/assets/img/postamat--inactive.png',
                            placemarkIconTerminal : '<?php echo DPD_PLUGIN_URI; ?>assets/js/dpd.widgets.map/assets/img/terminal--inactive.png',
                            
                            placemarkIconActive   : '<?php echo DPD_PLUGIN_URI; ?>assets/js/dpd.widgets.map/assets/img/pickup_locationmarker_highlighted.png',
                        }, {
                            tariffs: <?= json_encode($tariffs) ?>,
                            terminals: <?= json_encode($terminals) ?>
                        }
                    ).on('dpd.map.terminal.select', function(e, terminal, widget) {
                        parent.postMessage({type:'select', data: terminal}, '*');
                    })

                    $('#cancel').click(function(){
                        parent.postMessage({type:'close'}, '*');
                    });

                    // hack
                    // setTimeout(function() {
                    //     $('#dpd-map').data('dpd.map').setCenter();
                    // }, 1000);

                }, 500);
            })
        })(jQuery)
    </script>
</head>
<body style="margin:0">
    <div id="dpd-map"></div>
    <a href="#" id="cancel">&#10005;</a>
</body>
</html>