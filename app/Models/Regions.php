<?php

namespace App\Models;

use CodeIgniter\Model;

class Regions extends Model
{
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'name'];
    protected $returnType    = 'App\Entities\Region';

    protected $useTimestamps = false;

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    protected function db() {
        $this->db = \Config\Database::connect('region');
        return $this->db;
    }

    public function insertBatch(?array $set = null, ?bool $escape = null, int $batchSize = 100, bool $testing = false)
    {
        $this->db()->table('subdistricts')->insertBatch($set);
    }

    public function provinces()
    {
        return $this->db()->table('provinces')->select()->get()->getResult($this->returnType);
    }

    public function citys($province)
    {

        $where[is_numeric($province) ? 'a.province_id' : 'b.name']  = $province;

        return $this->db()->table('citys')->select('a.*, b.name as province_name')
            ->from('citys a')
            ->join('provinces b', 'b.id=a.province_id')
            ->groupBy('a.id')
            ->where($where)->get()->getResult($this->returnType);
    }

    public function subdistricts($city)
    {
        $where[is_numeric($city) ? 'a.city_id' : 'b.name']  = $city;

        return $this->db()->table('citys')->select('a.*, b.name as city_name')
            ->from('subdistricts a')
            ->join('citys b', 'b.id=a.city_id')
            ->groupBy('a.id')
            ->where($where)->get()->getResult($this->returnType);
    }

    public function search($search, $limit = 10, $offset = 0)
    {
        $query = $this->db()->table('subdistricts')->select('
        a.name as subdistrict_name, 
        b.name as city_name,
        b.type,
        c.name as province_name
        ')
            ->from('subdistricts a')
            ->join('citys b', 'b.id=a.city_id')
            ->join('provinces c', 'c.id=b.province_id')
            ->groupBy('a.id')
            ->like('a.name', $search)->orLike('b.name', $search)->orLike('c.name', $search)
            ->limit($limit, $offset)->get()->getResultArray();

        $results = [];
        foreach ($query as $value) {
            $type = ($value['type'] == 'Kota') ? $value['type'] . '. ' : 'Kab. ';
            array_push(
                $results,
                $value['province_name'] . ', ' . $type . $value['city_name'] . ', ' . $value['subdistrict_name']
            );
        }

        return $results;
    }

    public function getCity($cityName)
    {
        $reg = preg_replace("/^(Kabupaten|Kota\.|Kota|Kab\.|City|Districts)\s/", "", $cityName);
        return $this->db()->table('citys')->select()
            ->where('name', $reg)->get()->getRow(9, $this->returnType);
    }

    public function getSubDistrict($cityName, $subDistrictName)
    {
        return $this->db()->table('citys')->select('a.*, b.name as city_name')
            ->from('subdistricts a')
            ->join('citys b', 'b.id=a.city_id')
            ->groupBy('a.id')
            ->where(['a.name' => $subDistrictName, 'b.name' => $cityName])
            ->get()->getRow(0, $this->returnType);
    }
}
