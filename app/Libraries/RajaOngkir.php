<?php

namespace App\Libraries;

class RajaOngkir
{

    public function cost($data)
    {
        $dataPost = '';
        foreach ($data as $key => $value) {
            $dataPost .= $key.'='.$value.'&';
        }
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.rajaongkir.com/starter/cost",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $dataPost,
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded",
                "key: ".Setting::getApiKeyRajaOngkir()
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        if ($err) {
            return null;
        } else if(is_string($response)) {
            $rajaongkir = json_decode($response)->rajaongkir;
            if ($rajaongkir->status->code === 200) {
                return $rajaongkir->results[0]->costs;
            } else {
                return null;
            }
        }
    }

    private function mock() {
        return json_decode('{
            "rajaongkir": {
                "query": {
                    "courier": "pos",
                    "origin": "46",
                    "destination": "153",
                    "weight": 10000
                },
                "status": {
                    "code": 200,
                    "description": "OK"
                },
                "origin_details": {
                    "city_id": "46",
                    "province_id": "14",
                    "province": "Kalimantan Tengah",
                    "type": "Kabupaten",
                    "city_name": "Barito Utara",
                    "postal_code": "73881"
                },
                "destination_details": {
                    "city_id": "153",
                    "province_id": "6",
                    "province": "DKI Jakarta",
                    "type": "Kota",
                    "city_name": "Jakarta Selatan",
                    "postal_code": "12230"
                },
                "results": [
                    {
                        "code": "pos",
                        "name": "POS Indonesia (POS)",
                        "costs": [
                            {
                                "service": "Paket Kilat Khusus",
                                "description": "Paket Kilat Khusus",
                                "cost": [
                                    {
                                        "value": 485000,
                                        "etd": "4 HARI",
                                        "note": ""
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }
        }');
    }
}
