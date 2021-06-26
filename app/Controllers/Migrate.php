<?php

namespace App\Controllers;

class Migrate extends \App\Controllers\BaseResourceController
{

    public function index()
    {
        $table = $this->request->getGet('table');
        $isDelete = $this->request->getGet('is_delete');
        $isCreate = $this->request->getGet('is_create');
        $isSeeder = $this->request->getGet('is_seeder');
        $migrations = $this->directoryName('app/Database/Migrations');
        $seeds = $this->directoryName('app/Database/Seeds');
        if (!$isSeeder) {
            foreach ($migrations as $key => $migration) {
                if (isset($table)) {
                    if ($migration['name'] === ucfirst($table)) {
                        $class = new $migration['class'];
                        if ($isDelete) {
                            $class->down();
                        }
                        $class->up();
                    }
                } else {
                    $class = new $migration['class'];
                    if ($isDelete) {
                        $class->down();
                    }
                    $class->up();
                }
            }
        }

        
        if ($isCreate === 'true') {
            return $this->respond(['status' => 'ok']);
        }

        $seeder = \Config\Database::seeder();
        foreach ($seeds as $key => $seed) {
            if (isset($table)) {
                if ($seed['name'] === ucfirst($table) . 'Seeder') {
                    $seeder->call($seed['name']);
                }
            } else {
                $seeder->call($seed['name']);
            }
        }
        return $this->respond(['status' => 'ok']);
    }

    public function directoryName($path)
    {
        $filename = ROOTPATH . $path;
        $files = [];
        $pathClss = explode('/', $path);
        foreach ($pathClss as $key => $value) {
            $pathClss[$key] = ucfirst($value);
        }
        if (is_dir($filename)) {
            if ($handle = opendir($filename)) {
                while (($file = readdir($handle)) !== false) {
                    $ext = explode('.', $file);
                    if (count($ext) == 2) {
                        if ($ext[count($ext) - 1] == 'php') {
                            array_push($files, [
                                'name' => $ext[0],
                                'class' =>  implode('\\', $pathClss) .'\\'. $ext[0]
                            ]);
                        }
                    }
                }
                closedir($handle);
            }

            sort($files);
        }

        return $files;
    }

    public function generate_product() {
        helper('text');
        $file = file_get_contents(ROOTPATH . 'public/files/csvjson.json');
        $obj = json_decode($file, true);
        $products = [];
        foreach ($obj as $value) {
            $price = explode(' ', $value['Price']);
            $image = $this->uploadFromUrl($value['Thumbnail']);
            array_push($products, [
                'name' => $value['Name'],
                'min_order' => 1,
                'price' => substr($price[0], 1, strlen($price[0]) - 2) . '000',
                'description' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
                'unit' => 'buah',
                'stock' => 100,
                'thumb' => $image,
                'weight' => 400,
                'store_id' => 1,
                'category_id' => 1,
                'is_activated' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $productModel = new \App\Models\Products();
        $productModel->insertBatch($products);
    }

    public function test() {
        helper('text');
        $categoryModel = new \App\Models\Categories();
        $p = $this->request->getGet('ct');
        $subs = $categoryModel->where(['is_activated' => true, 'parent' => $p])->getResult();
        $thumbs = [];

        if ($handle = opendir(ROOTPATH. 'public/images/' .$p.'/')) {
            while (($file = readdir($handle)) !== false) {
                if ($file !== '..' && $file !== '.') {
                    array_push($thumbs, $file);
                }
            }
            closedir($handle);
        }
        sort($thumbs, SORT_NATURAL);

        //return $this->respond($thumbs);
        $updates = [];
        chdir(ROOTPATH. 'public/images/' .$p.'/');
        foreach ($subs as $key => $value) {
            $name = $p .'_' .random_string('numberic') . '.png';
            \Config\Services::image()
                ->withFile(ROOTPATH. 'public/images/' .$p.'/' . $thumbs[$key])
                ->save(ROOTPATH. 'public/images/' . $name);
            array_push($updates, ['id' => $value->id, 'thumb' => $name]);
        }
        $categoryModel->updateBatch($updates, 'id');

        return $this->respond($updates);
    }

    public function test2() {

        $productModel = new \App\Models\Products();
        $list = $productModel->findProduct([1,2,3,4,5]);
        $path = ROOTPATH . 'public/images/';
        foreach ($list as $value) {
            foreach ($value->thumbs as $image) {
                $info = \Config\Services::image()
                ->withFile($path . $image)
                ->getFile()
                ->getProperties(true);
                if ($info['width'] > 1000 || $info['height'] > 1000) {
                    \Config\Services::image()
                        ->withFile($path . $image)
                        ->resize(800, 800, true)
                        ->save($path . $image);
                }
        
                \Config\Services::image()
                    ->withFile($path . $image)
                    ->resize(200, 200, true)
                    ->save($path . 'thumbnails/' . $image);
            }
        }
    }

    private function uploadFromUrl($url)
    {
        $content = curl_get_file_contents($url);
        $path = ROOTPATH . 'public/images/';
        $profileName = random_string(). '.jpg';
        file_put_contents($path . $profileName, $content);
        $info = \Config\Services::image()
            ->withFile($path . $profileName)
            ->getFile()
            ->getProperties(true);

        if ($info['width'] > 1000 || $info['height'] > 1000) {
            \Config\Services::image()
                ->withFile($path . $profileName)
                ->resize(800, 800, true)
                ->save($path . $profileName);
        }

        \Config\Services::image()
            ->withFile($path . $profileName)
            ->resize(200, 200, true)
            ->save($path . 'thumbnails/' . $profileName);
        return $profileName;
    }
}
