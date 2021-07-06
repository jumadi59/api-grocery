<?php

namespace App\Filters;

use App\Libraries\Setting;
use App\Libraries\Token;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class Cors implements FilterInterface
{

  
  public function before(RequestInterface $request, $arguments = null)
  {
    
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method, Authorization, Application");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header('Content-Type: application/json');
    
    $whitelistIP = array("103.58.103.177", "114.125.253.195");
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
      //header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
      //header('Access-Control-Allow-Headers: Content-Type');
      exit();
    } else if (!in_array($this->get_client_ip(), $whitelistIP)) {
        if ( !isset($_SERVER['HTTP_APPLICATION']) || $_SERVER['HTTP_APPLICATION'] !== Setting::getAppID()) {
            http_response_code(401);
            echo json_encode([
                'status'   => 401,
                'message' =>  'Unauthorized'
            ]);
            exit();
        }
        $this->user();
    }
  }

  public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
  {
  }

  private function get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
  }
  protected function user()
  {
    $userModel = new \App\Models\Users();
    $decoded = Token::get();
    if ($decoded) {
      $data = $userModel->user($decoded->data->id);
      if ($data) {
        $userModel->update_activity($decoded->data->id);
        return $data;
      }
    }
    return null;
  }
}
