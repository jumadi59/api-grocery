<?php

namespace App\Controllers;

use App\Controllers\BaseResourceController;
use App\Libraries\Setting;
use App\Models\CouponClaims;

class Coupons extends BaseResourceController
{

    protected $modelName = 'App\Models\Coupons';
    protected $format    = 'json';

    public function index()
    {
        $limit = $this->request->getGet('limit') ?: 10;
        $offset = $this->request->getGet('offset') ?: 0;

        $user = $this->user();
        $couponClaimModel = new CouponClaims();
        $data = $couponClaimModel->coupons($user->id, $this->request->getGet('get'));
        if (count($data) > 0) {
            return $this->respond($data);
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function couponStore($sId = null) {
        $user = $this->user();
        if ($user) {
            $userId = $user->id;
        } else {
            $userId = null;
        }
        if ($sId) {
            $data = $this->model->coupons($sId, $userId);
            if (count($data) > 0) {
                return $this->respond($data);
            } else {
                return $this->respondNoContent("Empty data");
            }
        } else {
            return $this->respondNoContent("Empty data");
        }
    }

    public function show($id = null)
    {
        $user = $this->user();
        $data = $this->model->coupon($id, $user != null ? $user->id: null);
        if ($data) {
            $data->terms = [
                lang('App.coupons.' . $data->type) . " sebesar " . ($data->unit === 'amount' ? format_rupiah($data->value) : $data->value.'%') . " untuk pembelanjaan di Toko dengan Power Merchant & Official Store.",
                $data->min_transaction > 0 ? "Minimum transaksi " . format_rupiah($data->min_transaction) : "Tanpa minimum transaksi.",
                "Hanya berlaku untuk satu kali pembelanjaan di Toko dengan Power Merchant & Official Store " . Setting::getAppName() . ".",
                "Promo hanya berlaku di Aplikasi " . Setting::getAppName() . ".",
                "Untuk mendapatkan benefit pastikan Aplikasi anda sudah terupdate.",
                "Tidak bisa digabung dengan promo lain.",
                "Sesua kebijakan terbaru dari Google Play Store, promo tidak berlaku untuk produk tertentu(produk tembakau, roko elektrik, dan turunanya) khusus di aplikasi Android.",
                "Satu pengguna " . Setting::getAppName() . " hanya boleh menggunakan 1 (satu) akun " . Setting::getAppName() . " untuk mengikuti promo ini. jika ditemukan pembuatan lebih dari 1 (satu) akun oleh 1 (satu) pengguna yang sama dan/atau alamat yang sama dan/atau ID pengguna yang sama dan/atau identitas pembayaran yang sama dan/atau riwayat transaksi yang sama maka pengguna tidak mendapatkan benefiit dari promo " . Setting::getAppName() . ".",
                "<b>" . Setting::getAppName() . " berhak melakukan tindakan yang diperlukan apabila diduga terjadi tindakan kecurangan dari pengguna yang merugikan pihak " . Setting::getAppName() . ".</b>",
                "Dengan menggunakan Voucher ini, pengguna dianggap mengerti dan menyetujui semua Syarat & Ketentuan yang berlaku."
            ];
            return $this->respond($data);
        } else {
            return $this->failNotFound('No Data Found with id ' . $id);
        }
    }

    public function claim()
    {
        $user = $this->user();
        $couponClaimModel = new CouponClaims();
        $code = $this->request->getGet('code');
        $result = $this->model->getWhere(['code' => $code])->getRow();
        if ($result) {
            $isClaim = $couponClaimModel->getWhere(['coupon_id' => $result->id, 'user_id' => $user->id])->getRow();
            if ($isClaim || (is_int($result->stock) && $result->stock == 0)) {
                return $this->respond([
                    'status'   => 203,
                    'message' => $result->stock == 0 ? 'Voucher telah habis' : 'Anda sudah memiliki voucher'
                ], 203);
            }

            $result2 = $couponClaimModel->insert([
                'coupon_id' => $result->id,
                'user_id' => $user->id
            ]);
            if ($result2) {
                if (is_int($result->stock)) {
                    $this->model->update($result->id, ['stock' => $result->stock - 1]);
                }
                $data = $this->model->coupon($result->id, $user->id);
                return $this->respondCreated([
                    'status'   => 200,
                    'data'     => $data,
                    'message' =>  'Data save'
                ], 'Data create');
            } else {
                return $this->respond([
                    'status'   => 406,
                    'error'    => $result2,
                    'message' =>  'gagal save'
                ], 406);
            }
        } else {
            return $this->failNotFound('Code voucher ' . $code . 'not found');
        }
    }

}
