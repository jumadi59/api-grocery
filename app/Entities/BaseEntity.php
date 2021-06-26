<?php

namespace App\Entities;

use CodeIgniter\Entity;

class BaseEntity extends Entity
{

    protected $simpleName = 'base';
    public $querys = [];
    protected $objects = [];
    public $tmpData = [];

    public function setAttributes(array $data)
    {
        $newData = [];
        foreach ($data as $key => $value) {
            if (!property_exists($this, $key)) {
                $explode = explode('-', $key);
                if (count($explode) > 1) {
                    $end = $explode[count($explode) - 1];
                    $start = $explode[0];
                } else {
                    $explode = explode('_', $key);
                    $end = $explode[count($explode) - 1];
                    unset($explode[count($explode) - 1]);
                    $start = implode('_', $explode);
                }
                if (!empty($value) && $value != '0') {
                    if (property_exists($this, $end) && $start === $this->simpleName) {
                        $newData[$end] = $value;
                    } else {
                        $this->tmpData[$key] = $value;
                    }
                }
            } else if (!empty($value) && $value != '0') {
                $newData[$key] = $value;
            }
        }
        if ($this->simpleName === 'chat') {
        
        }
        $result = parent::setAttributes($newData);
        $this->setObjects();
        return $result;
    }

    public function setObjects()
    {
        foreach ($this->objects as $className => $tdClass) {
            if (property_exists($this, $className)) {
                $entity = new $tdClass;
                $entity->setAttributes($this->tmpData);
                $this->tmpData = $entity->tmpData;
                $entity->setObjects();
                if (count($entity->attributes) > 0) $this->attributes[$className] = $entity;
            }
        }
        return $this;
    }

    protected function mutateDate($value)
    {
        if ($value != null && $this->isDateEmpty($value)) {
            return null;
        }
        return parent::mutateDate($value);
    }

    public function isDateEmpty($date)
    {
        $exp = explode(' ', $date);
        return $exp[0] === '0000-00-00';
    }

    public function isTimeEmpty($time)
    {
        $exp = explode(' ', $time);
        if (count($exp) === 2) {
            return $exp[1] === '00:00:00';
        } else {
            return $time === '00:00:00';
        }
    }
}
