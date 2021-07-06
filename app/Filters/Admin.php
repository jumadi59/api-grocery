<?php

namespace App\Filters;

use App\Libraries\Token;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Admin implements FilterInterface
{

  public function before(RequestInterface $request, $arguments = null)
  {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header('Content-Type: application/json');

    $method = $_SERVER['REQUEST_METHOD'];
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
      //header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
      //header('Access-Control-Allow-Headers: Content-Type');
      exit();
    } else {
      if ($this->admin() == null) {
        http_response_code(401);
        echo json_encode([
          'status'   => 401,
          'message' =>  'Unauthorized'
        ]);
        exit();
      }
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
  }

  protected function admin()
  {
    $userModel = new \App\Models\Users();
    $decoded = Token::get();
    if ($decoded) {
      $data = $userModel->user($decoded->data->id);
      if ($data) {
        if ($data->role == 1) {
          //$userModel->update_activity($decoded->data->id);
          return $data;
        }
      }
    }
    return null;
  }
}
