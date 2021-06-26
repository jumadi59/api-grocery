<?php

namespace App\Database\Seeds;

class DiscoverSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $discover = array(
            array('id' => 'flash_sale','title' => 'Flash Sale','sub_title' => '','banner' => 'banner_slide.jpg','banner_left' => NULL,'background_color' => NULL,'filters' => '{"discount.type":"flash_sale"}'),
            array('id' => 'new','title' => 'New Products','sub_title' => '','banner' => 'banner_slide_1.jpg','banner_left' => 'banner_left.png','background_color' => '#1877f2','filters' => '{"sort":"created_at.desc"}'),
            array('id' => 'sold','title' => 'Sold Products','sub_title' => '','banner' => NULL,'banner_left' => NULL,'background_color' => NULL,'filters' => '{"sort":"sold.desc","product.sold >=":1}')
          );
        $this->db->table('discover')->insertBatch($discover);
    }
}
