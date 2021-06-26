<?php

namespace App\Models;

use CodeIgniter\Model;

class Verify extends Model
{
    protected $table = 'verify';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'code', 'data', 'expired_at'];
    //protected $returnType    = 'App\Entities\Verify';

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function actionData($id, $data)
    {
        helper('array_helper');
        $userFields = ['username', 'email', 'phone', 'verified_email', 'verified_phone'];
        $countTrue = 0;
        foreach ($userFields as $key) {
            $d = dot_array_search($key, $data['field']);
            if ($d) {
                $countTrue +=1;
            }
        }
        if ($countTrue == 0) {
            $this->delete($id);
            return;
        }
        switch ($data['action']) {
            case 'update':
                $this->db->table($data['table'])->update($data['field'], ['id' => $data['where_id']]);
                break;
            case 'delete':
                $this->db->table($data['table'])->delete(['id' => $data['where_id']]);
                break;
            default:
                break;
        }
        $this->delete($id);
    }
}
