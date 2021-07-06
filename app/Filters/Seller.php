<?php

namespace App\Filters;

use App\Libraries\Token;
use CodeIgniter\HTTP\RequestInterface;

class Seller extends Cors
{

  public function before(RequestInterface $request, $arguments = null)
  {
    //parent::before($request, $arguments);
    if ($this->store() == null) {
      http_response_code(401);
      echo json_encode([
        'status'   => 401,
        'message' =>  'Unauthorized'
      ]);
      exit();
    }
  }

  protected function store()
  {
    $userModel = new \App\Models\Users();
    $decoded = Token::get();
    if ($decoded) {
      $data = $userModel->user($decoded->data->id);
      if ($data) {
        //$userModel->update_activity($decoded->data->id);
        $storeModel = new \App\Models\Stores();
        $store = $storeModel->storeUser($data->id);
        return $store;
      }
    }
    return null;
  }
}
