<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;
use App\Models\Address;
use App\Models\Regions;

class Couriers extends BaseResourceController
{

    protected $modelName = 'App\Models\Couriers';
    protected $format    = 'json';

    public function index()
    {
        return $this->respond($this->model->where(['is_activated' => true])->getResult());
    }

    public function all() {
        $limit = $this->request->getGet('limit') ?: 10;
        $offset = $this->request->getGet('offset') ?: 0;
        return $this->respond($this->model->findAll($limit, $offset));
    }

    public function show($id = null)
    {
        $data = $this->model->where(['id' => $id])->getRow();
        if ($data) {
            if (count($data) > 0) {
                return $this->respond($data);
            }
        }
        return $this->failNotFound('No Data Found with id ' . $id);
    }

    public function courier_store($id) {
        $storeModel = new \App\Models\Stores();
        $store = $storeModel->store($id);
        if ($store) {
            $data = $this->model->find($store->courier_active);
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }

    }

    //RajaOngkir Pro
    public function cost2()
    {
        $storeModel = new \App\Models\Stores();
        $regionModel = new Regions();

        $weight = $this->request->getGet('weight');
        $storeId = $this->request->getGet('store_id');
        $destination = $this->request->getGet('destination');
        $explode = explode(',', $destination);
        $destinationSubDistrict = $regionModel->getSubDistrict($explode[0], $explode[1]);

        $data = $storeModel->store($storeId);
        if ($data && $destinationSubDistrict) {
            $originSubDistrict = $regionModel->getSubDistrict(
                $data->address->city,
                $data->address->subdistrict
            );
            $couriers = [];

            foreach ($data->courier_active as $v) {
                $result = $this->model->find($v);
                if (count($result) == 1) {
                    $courier = $result[0];
                    $rajaOngkir = new \App\Libraries\RajaOngkir();
                    if ($courier->code == 'fe') {
                        $entity = new \App\Entities\Courier();
                        $entity->setAttributes([
                            'id'            => $courier->id,
                            'name'          => $courier->simple_name,
                            'service'       => '',
                            'icon'          => $courier->icon,
                            'description'   => '',
                            'note'          => '',
                            'cost'          => 2000,
                            'etd'           => ''
                        ]);
                        array_push($couriers, $entity);
                    } else {
                        $ongkir = $rajaOngkir->cost([
                            'courier'       => $courier->code,
                            'origin'        => $originSubDistrict->id,
                            'destination'   => $destinationSubDistrict->id,
                            'weight'        => $weight
                        ]);
                        foreach ($ongkir as $o) {
                            $entity = new \App\Entities\Courier();
                            $entity->setAttributes([
                                'id'            => $courier->id,
                                'name'          => $courier->simple_name,
                                'service'       => $o->service,
                                'icon'          => $courier->icon,
                                'description'   => $o->description,
                                'note'          => $o->cost[0]->note,
                                'cost'          => $o->cost[0]->value,
                                'etd'           => $this->parseEtd($o->cost[0]->etd)
                            ]);
                            array_push($couriers, $entity);
                        }
                    }
                }
            }
            $resultData['id'] = (int)$storeId;
            $resultData['total'] = count($couriers);
            return $this->respond([
                'data' => $resultData,
                'results' => $couriers
            ]);
        } else {
            return $this->failNotFound('No Data Found');
        }
    }

    //RajaOngkir Basic
    public function cost()
    {
        $storeModel = new \App\Models\Stores();
        $addressModel = new Address();
        $regionModel = new Regions();

        $weight = $this->request->getGet('weight');
        $storeId = $this->request->getGet('store_id');
        $destination = $this->request->getGet('destination');
        if (is_numeric($destination)) {
            $destination = $addressModel->detail_address($destination)->city;
        }
        $destinationCity = $regionModel->getCity($destination);

        $data = $storeModel->store($storeId);
        if ($data && $destinationCity) {
            $originCity = $regionModel->getCity($data->address->city);
            $couriers = [];
            foreach ($data->courier_active as $v) {
                $courier = $this->model->find($v);
                if ($courier) {
                    $rajaOngkir = new \App\Libraries\RajaOngkir();
                    if ($courier->code == 'fe' && $destinationCity->id === $originCity->id) { 
                        $entity = new \App\Entities\Courier();
                        $entity->setAttributes([
                            'id'            => $courier->id,
                            'name'          => $courier->simple_name,
                            'service'       => '',
                            'icon'          => $courier->icon,
                            'description'   => '',
                            'note'          => '',
                            'cost'          => 2000,
                            'etd'           => '> 1 jam'
                        ]);
                        array_push($couriers, $entity);
                    } else {
                        $ongkir = $rajaOngkir->cost([
                            'courier'       => $courier->code,
                            'origin'        => $originCity->id,
                            'destination'   => $destinationCity->id,
                            'weight'        => $weight
                        ]);
                        foreach ($ongkir as $o) {
                            $entity = new \App\Entities\Courier();
                            $entity->setAttributes([
                                'id'            => $courier->id,
                                'name'          => $courier->simple_name,
                                'service'       => $o->service,
                                'icon'          => $courier->icon,
                                'description'   => $o->description,
                                'note'          => $o->cost[0]->note,
                                'cost'          => $o->cost[0]->value,
                                'etd'           => $this->parseEtd($o->cost[0]->etd)
                            ]);
                            array_push($couriers, $entity);
                        }
                    }
                }
            }
            $resultData['id'] = (int) $storeId;
            $resultData['total'] = count($couriers);
            return $this->respond([
                'data' => $resultData,
                'results' => $couriers
            ]);
        } else {
            return $this->failNotFound('No Data Found');
        }
    }

    private function parseEtd($etd)
    {
        $explode = explode(' ', $etd);
        return strlen($explode[0]) > 1 ? $explode[0] . ' ' . lang('day')  : '> ' . $explode[0] . ' ' . lang('day');
    }

    public function create()
    {
    }

    public function update($id = null)
    {
    }

    public function delete($id = null)
    {
    }
}
