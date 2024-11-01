<?php

namespace DPD\Model;

class Terminal extends Model {

    public function __construct($DPDconfig) {
        $db = \Ipol\DPD\DB\Connection::getInstance($DPDconfig);
        $this->table = $db->getTable('terminal');
    }

    public static function onlyAvaliable(array $terminals, \Ipol\DPD\Shipment $shipment)
    {
        $avaliable = [];
        foreach ($terminals as $item) {
            if ($item->checkShipment($shipment)) {
                $avaliable[] = $item;
            }
        }

        return $avaliable;
    }

    public static function onlyAvaliableNpp(array $terminals, \Ipol\DPD\Shipment $shipment, $nppSum)
    {
        $avaliable = [];
        foreach ($terminals as $item) {
            if ($item['NPP_AVAILABLE'] == 'Y' && $item['NPP_AMOUNT'] >= $nppSum) {
                $avaliable[] = $item;
            }
        }
        return $avaliable;
    }

     public static function onlyAvaliableByDimessions(
        array $terminals, \Ipol\DPD\Shipment $shipment
    ) {
        $avaliable = [];
        foreach ($terminals as $item) {
            if ($item->checkShipmentDimessions($shipment)) {
                $avaliable[] = $item;
            }
        }

        return $avaliable;
    }


    public static function checkSelectedTerminalOnExists(array $terminals, $terminalCode)
    {
        foreach ($terminals as $terminal) {
            if ($terminal['CODE'] == $terminalCode) {
                return true;
            }
        }
        return false;
    }

    public static function renderTerminalsOptions($terminals)
    {
        $html = '';
        foreach ($terminals as $item) {
            $html .= '<option value="'.$item['CODE'].'">'.
                $item['ADDRESS_SHORT'].'</option>';
        }
        return $html;
    }
} 

?>