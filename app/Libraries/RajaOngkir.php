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
            CURLOPT_URL => "https://api.rajaongkir.com/starter/cost",
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
        } else {
            $rajaongkir = json_decode($response)->rajaongkir;
            if ($rajaongkir->results) {
                return $rajaongkir->results[0]->costs;
            } else {
                return null;
            }
        }
    }
}
