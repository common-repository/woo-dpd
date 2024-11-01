<?php

namespace DPD\Helper;

class View {

    public static function load($path, $data = [])
    {
        foreach ($data as $key => $value) {
            $$key = $value;
        }
        ob_start();
        require_once DPD_PLUGIN_PATH.'views/'.$path.'.php';
        $view = ob_get_contents();
        ob_end_clean();
        return $view;
    }

}