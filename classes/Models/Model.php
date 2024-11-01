<?php

namespace DPD\Model;

class Model {

    protected $table;
    protected $select;
    protected $where;
    protected $order;
    protected $limit;


    public function get()
    {
        $conditions = array_merge($this->where  ? : [], $this->order ? : [], $this->limit ? : [],
            $this->select ? : []);
        return $this->table->findModels($conditions);

    }

    public function select($select)
    {
        $this->select = [
            'select' => $select
        ];
        return $this;
    }

    public function row()
    {
        $this->limit['limit'] = '0,1';
        $result = $this->get();
        if (isset($result[0])) {
            return $result[0];
        }
        return null;
    }

    public function where($where, $bind)
    {
        $this->where = [
            'where' => $where,
            'bind' => $bind
        ];
        return $this;
    }

    public function order($order)
    {
        $this->order = ['order' => $order];
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = ['limit' => $limit];
        return $this;
    }

}