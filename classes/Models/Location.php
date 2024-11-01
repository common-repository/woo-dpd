<?php

namespace DPD\Model;

class Location extends Model {

    public function __construct($DPDconfig) {
        $db = \Ipol\DPD\DB\Connection::getInstance($DPDconfig);
        $this->table = $db->getTable('location');
    }

    public static function formAutoCompleteArray(array $locations, $countryCode = null)
    {
        $result = [];
        foreach ($locations as $item) {
            $name = explode(',', $item['ORIG_NAME']);
            $result[] = [
                $countryCode && strtoupper($countryCode) == strtoupper($item['COUNTRY_CODE'])
                    ? trim(str_replace($item['COUNTRY_NAME'], '', $item['ORIG_NAME']), ' ,')
                    : $item['ORIG_NAME']
                ,

                $item['CITY_ID']
            ];
        }

        return $result;
    }
} 

?>