<?php namespace App\Models;
 
class Trackings extends BaseModel
{
    protected $table = 'trackings';
    protected $primaryKey = 'id';
    protected $fieldSearch = 'name';
    protected $allowedFields = [
        'order_id', 'name', 'description', 'status', 'created_at',]; 
    protected $returnType    = 'App\Entities\Tracking';

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function trackings($orderId)
    {
        return $this->builder()->select('id, name, status, description, created_at')
        ->where('order_id', $orderId)->get()->getResult($this->returnType);
    }

}