<?php

namespace App\Modules\Support\Controllers\Admin;

use App\Controllers\BaseController;

class SupportSettingsController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $settings = $this->db->table('support_settings')->get()->getResultArray();
        $map = array_column($settings, 'setting_value', 'setting_key');

        $data = [
            'title' => 'AI Configuration',
            'settings' => $map
        ];

        return view('App\Modules\Support\Views\admin\settings\index', $data);
    }

    public function save()
    {
        $posted = $this->request->getPost();
        
        foreach ($posted as $key => $value) {
            $this->db->table('support_settings')
                ->where('setting_key', $key)
                ->update(['setting_value' => $value, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        return redirect()->to(base_url('admin/support/settings'))->with('success', 'AI Settings updated successfully.');
    }
}
