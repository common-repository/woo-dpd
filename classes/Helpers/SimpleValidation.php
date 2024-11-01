<?php

namespace DPD\Helper;

class SimpleValidation {

    public static function validate($data)
    {
        $errors = [];
        foreach ($data as $item) {
            if (!is_array($item) 
                || !array_key_exists('value', $item) 
                || !isset($item['type'], $item['error'])
            ) {
                throw new \Exception('Wrong data for validation');
            }
            $error = '';
            $type = explode('|', $item['type']);
            if (!$item['value'] && ((count($type) > 1 && $type[1] == 'required')
                || $type[0] == 'required')
            ) {
                $error = $item['error'];
            } else {
                switch ($type[0]) {
                    case 'number':
                        if (!is_numeric($item['value'])) {
                            $error = $item['error'];
                        }
                        break;

                    case 'email':
                        if (!filter_var($item['value'], FILTER_VALIDATE_EMAIL)) {
                            $error = $item['error'];
                        }
                        break;

                    case 'date':
                        if (!strtotime($item['value'])) {
                            $error = $item['error'];
                        }
                        break;
                }
            }
            if ($error) {
                $errors[] = $error;
            }
        }
        return $errors;
    }

    public static function errorsHtml($errors)
    {
        $html = '';
        foreach ($errors as $error) {
            $html .= '<p>'.$error.'</p>';
        }
        return $html;
    }
}