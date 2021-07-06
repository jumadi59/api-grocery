<?php

namespace App\Filters;

use App\Libraries\Token;
use CodeIgniter\HTTP\RequestInterface;

class Customer extends Cors
{

  public function before(RequestInterface $request, $arguments = null)
  {
      //parent::before($request, $arguments);
      if ($this->user() == null) {
        http_response_code(401);
        echo json_encode([
          'status'   => 401,
          'message' =>  'Unauthorized'
        ]);
        exit();
      }
  }

  protected function user()
  {
    $userModel = new \App\Models\Users();
    $decoded = Token::get();
    if ($decoded) {
      $data = $userModel->user($decoded->data->id);
      if ($data) {
        //$userModel->update_activity($decoded->data->id);
        return $data;
      }
    }
    return null;
  }
}
