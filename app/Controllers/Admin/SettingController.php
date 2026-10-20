<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;

class SettingController extends BaseController
{
    protected $settingModel;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
    }

    public function index()
    {
        // Default fallbacks to prevent empty display
        $defaults = [
            'hero_subtitle'     => 'Filosofi dari Hulu ke Hilir',
            'hero_title'        => 'Menyeduh Kebersamaan, Merayakan perjuangan Petani Kopi.',
            'hero_description'  => '"Di balik kepekatan secangkir kopi, ada tetes keringat petani yang merawat tunas, kejelian roaster yang mencari karakter rasa, dan kehangatan obrolan yang tercipta di setiap meja."',
            'about_title'       => 'Berkomitmen pada kualitas dari hulu hingga ke hilir.',
            'about_description' => 'Kami percaya bahwa kopi yang baik lahir dari ekosistem yang sehat. Melalui program kolaborasi, kami mendampingi setiap tahapan produksi kopi demi menjaga keaslian rasa dan kualitas produk.',
            'contact_address'   => 'Jl. Raya Pandeglang No. 10, Pandeglang, Banten',
            'contact_email'     => 'kontak@ngopipanmas.or.id',
            'contact_phone'     => '+62 812-3456-7890',
        ];

        $settings = [];
        foreach ($defaults as $key => $defaultVal) {
            $settings[$key] = $this->settingModel->getVal($key, $defaultVal);
        }

        $data = [
            'title'    => 'Pengaturan Website (CMS)',
            'settings' => $settings
        ];

        return view('admin/settings/index', $data);
    }

    public function update()
    {
        $keys = [
            'hero_subtitle',
            'hero_title',
            'hero_description',
            'about_title',
            'about_description',
            'contact_address',
            'contact_email',
            'contact_phone'
        ];

        foreach ($keys as $key) {
            $val = $this->request->getPost($key);
            $this->settingModel->setVal($key, $val);
        }

        return redirect()->to('admin/settings')->with('message', 'Pengaturan website berhasil diperbarui.');
    }
}
