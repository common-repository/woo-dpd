<?php

namespace DPD;

class DataImport {

    private $importSteps;
    private $stepFunction;
    private $total = 0;
    private $offset = 0;
    private $step = 0;
    private $tableName;
    private $DPDconfig;


    public function __construct($step, $offset) {


        //инициализируем шаги импорта
        $this->importSteps = [
            'loadAll' => \Ipol\DPD\DB\Location\Agent::class,
            'loadCashPay' => \Ipol\DPD\DB\Location\Agent::class,
            'loadUnlimited' => \Ipol\DPD\DB\Terminal\Agent::class,
            'loadLimited' => \Ipol\DPD\DB\Terminal\Agent::class
        ];

        $this->step = $step;
        $this->offset = $offset;

        //определяем текущий шаг импорта и таблицу для импорта
        $stepsArray = array_keys($this->importSteps);
        if ($this->step != -1 && $this->step < count($this->importSteps)) {   
            $this->stepFunction = $stepsArray[$this->step];
            $this->tableName = $this->stepFunction == 'loadAll' || 
                $this->stepFunction == 'loadCashPay' ? 'location' : 'terminal';
        } else {
            throw new \Exception(__('Step not found', 'dpd')); 
        }

        global $DPDconfig;
        $this->DPDconfig = new \Ipol\DPD\Config\Config($DPDconfig);
    }

    /**
     * Запустить импорт
     * @return array
     */
    public function run()
    {
        
        if (!$this->DPDconfig->isActiveAccount()) {
            throw new \Exception(__('DPDconfig empty', 'dpd'));
        }
        
        $table  = \Ipol\DPD\DB\Connection::getInstance($this->DPDconfig)->getTable($this->tableName);
        $api    = \Ipol\DPD\API\User\User::getInstanceByConfig($this->DPDconfig);
        $loader = new $this->importSteps[$this->stepFunction]($api, $table);
        $result = $loader->{$this->stepFunction}($this->offset);
        
        if (is_array($result)) { 
            $this->offset = $result[0];
            $this->total = $result[1]; 
        } else {
            $this->offset = 0;
            $this->getNextStep();
        }

        return $result;
    }

    private function getNextStep()
    {
        $stepsArray = array_keys($this->importSteps);
        $index = array_search($this->stepFunction, $stepsArray);
        $this->step = array_key_exists($index + 1, $stepsArray) ? $index + 1 : -1;
    }

    public function getStep()
    {
        return $this->step;
    }

    public function getStepName()
    {
        $steps = [
            __('Import cities', 'dpd'),
            __('Import cities with cash pay', 'dpd'),
            __('Import terminals unlimited', 'dpd'),
            __('Import terminals with limited', 'dpd'),
        ];
        return array_key_exists($this->step, $steps) ?
            $steps[$this->step] : __('Import ended', 'dpd');
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getTotal()
    {
        return $this->total;
    }
}