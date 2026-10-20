<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class RegionController extends BaseController
{
    public function getCities()
    {
        $db = \Config\Database::connect();
        // Return cities, sorting Kota Depok (city_id '3276') first for convenience
        $cities = $db->table('cms_region02_cities')
            ->select('id, city_id, name')
            ->orderBy("CASE WHEN city_id = '3276' THEN 0 ELSE 1 END", "ASC")
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($cities);
    }

    public function getDistricts()
    {
        $cityId = $this->request->getVar('city_id');
        if (empty($cityId)) {
            return $this->response->setJSON([]);
        }

        $db = \Config\Database::connect();
        
        // Find standard city_id code if db ID was passed
        if (is_numeric($cityId)) {
            $city = $db->table('cms_region02_cities')->where('id', $cityId)->get()->getRowArray();
            $cityCode = $city ? $city['city_id'] : '';
        } else {
            $cityCode = $cityId;
        }

        $districts = $db->table('cms_region03_districts')
            ->select('id, district_id, name')
            ->where('city_id', $cityCode)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($districts);
    }

    public function getVillages()
    {
        $districtId = $this->request->getVar('district_id');
        if (empty($districtId)) {
            return $this->response->setJSON([]);
        }

        $db = \Config\Database::connect();

        // Find standard district_id code if db ID was passed
        if (is_numeric($districtId)) {
            $district = $db->table('cms_region03_districts')->where('id', $districtId)->get()->getRowArray();
            $districtCode = $district ? $district['district_id'] : '';
        } else {
            $districtCode = $districtId;
        }

        $villages = $db->table('cms_region04_villages')
            ->select('id, village_id, name')
            ->where('district_id', $districtCode)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON($villages);
    }
}
