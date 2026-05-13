<?php

namespace Modules\Setting\Controllers;

use App\Controllers\BaseController;
use Modules\IAM\Models\UserModel;
use Modules\IAM\Models\RoleModel;
use Modules\IAM\Models\PermissionModel;

class SettingController extends BaseController
{
    protected $settingsFile;

    public function __construct()
    {
        $this->settingsFile = WRITEPATH . 'settings.json';
    }

    public function index()
    {
        $settings = [];
        if (file_exists($this->settingsFile)) {
            $settings = json_decode(file_get_contents($this->settingsFile), true) ?? [];
        }

        $tab = $this->request->getGet('tab') ?? 'general';
        $staff = [];

        if ($tab == 'account') {
            $db = \Config\Database::connect();
            $builder = $db->table('users');
            $builder->select('users.*, roles.name as role_name, roles.description as role_desc');
            $builder->join('users_roles', 'users_roles.user_id = users.id', 'left');
            $builder->join('roles', 'roles.id = users_roles.role_id', 'left');
            $builder->where('users.deleted_at', null);
            $builder->orderBy('users.created_at', 'DESC');
            $staff = $builder->get()->getResult();
        }

        $roles = [];
        $permissions = [];
        $rolePermissions = [];

        if ($tab == 'permissions') {
            $roleModel = new RoleModel();
            $permissionModel = new PermissionModel();
            
            $roles = $roleModel->findAll();
            $permissions = $permissionModel->findAll();
            
            $db = \Config\Database::connect();
            $existing = $db->table('roles_permissions')->get()->getResult();
            
            foreach ($existing as $row) {
                $rolePermissions[$row->role_id][] = $row->permission_id;
            }
        }

        // Get SMS status info for the notifications tab
        $smsStatus = null;
        $smsStats = null;
        if ($tab == 'notifications') {
            try {
                $smsService = new \App\Libraries\SmsService();
                $smsStatus = $smsService->getStatus();

                // Get SMS message stats
                $db = \Config\Database::connect();
                if ($db->tableExists('sms_messages')) {
                    $smsStats = [
                        'total_sent'     => $db->table('sms_messages')->where('direction', 'outbound')->where('status', 'sent')->countAllResults(),
                        'total_failed'   => $db->table('sms_messages')->where('direction', 'outbound')->where('status', 'failed')->countAllResults(),
                        'total_received' => $db->table('sms_messages')->where('direction', 'inbound')->countAllResults(),
                        'today_sent'     => $db->table('sms_messages')->where('direction', 'outbound')->where('status', 'sent')->where('created_at >=', date('Y-m-d 00:00:00'))->countAllResults(),
                    ];
                }
            } catch (\Exception $e) {
                log_message('error', '[Settings] Failed to get SMS status: ' . $e->getMessage());
            }
        }

        $data = [
            'title' => 'Settings',
            'tab' => $tab,
            'settings' => $settings,
            'staff' => $staff,
            'roles' => $roles,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
            'smsStatus' => $smsStatus,
            'smsStats' => $smsStats,
        ];

        return view('Modules\Setting\Views\index', $data);
    }

    public function update()
    {
        $tab = $this->request->getPost('tab') ?? 'general';
        
        // Load existing settings
        $settings = [];
        if (file_exists($this->settingsFile)) {
            $settings = json_decode(file_get_contents($this->settingsFile), true) ?? [];
        }

        // Handle File Upload (Company Logo)
        $file = $this->request->getFile('company_logo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/settings', $newName);
            $settings['company_logo'] = $newName;
        }

        // Handle specific fields based on tab
        if ($tab == 'general') {
            $fields = [
                'company_name', 'company_address', 'company_city', 
                'company_state', 'company_country_code', 'company_zip_code', 
                'company_phone', 'company_vat', 'map_provider', 'google_maps_api_key'
            ];
            
            foreach ($fields as $field) {
                if ($this->request->getPost($field) !== null) {
                    $settings[$field] = $this->request->getPost($field);
                }
            }
        } elseif ($tab == 'notifications') {
            $fields = [
                // Pusher
                'pusher_app_id', 'pusher_key', 'pusher_secret', 'pusher_cluster',
                // SMS Provider Selection
                'sms_provider',
                // Twilio
                'twilio_sid', 'twilio_token', 'twilio_number',
                // Telnyx
                'telnyx_api_key', 'telnyx_number', 'telnyx_messaging_profile_id', 'telnyx_public_key',
            ];

            foreach ($fields as $field) {
                if ($this->request->getPost($field) !== null) {
                    $settings[$field] = $this->request->getPost($field);
                }
            }
        } elseif ($tab == 'templates') {
            $fields = [
                'tpl_trip_created', 'tpl_driver_arrived', 'tpl_trip_completed',
                'tpl_driver_offer', 'tpl_otp',
            ];

            foreach ($fields as $field) {
                if ($this->request->getPost($field) !== null) {
                    $settings[$field] = $this->request->getPost($field);
                }
            }
        } elseif ($tab == 'permissions') {
            $perms = $this->request->getPost('perms');
            $db = \Config\Database::connect();
            
            // Clear all existing permissions first (simplest approach for matrix update)
            $db->table('roles_permissions')->truncate();
            
            if ($perms && is_array($perms)) {
                $data = [];
                foreach ($perms as $roleId => $rolePerms) {
                    foreach ($rolePerms as $permId => $val) {
                        $data[] = [
                            'role_id' => $roleId,
                            'permission_id' => $permId
                        ];
                    }
                }
                
                if (!empty($data)) {
                    $db->table('roles_permissions')->insertBatch($data);
                }
            }
            
            return redirect()->to(base_url('settings?tab=permissions'))->with('success', 'Permissions updated successfully.');
        }

        // Save back to file
        file_put_contents($this->settingsFile, json_encode($settings, JSON_PRETTY_PRINT));

        return redirect()->to(base_url('settings?tab=' . $tab))->with('success', 'Settings updated successfully.');
    }

    public function clear_cache()
    {
        // Clear CodeIgniter Cache
        cache()->clean();

        // Clear view cache
        $files = glob(WRITEPATH . 'cache/*'); 
        foreach ($files as $file) {
            if (is_file($file) && !str_contains($file, 'index.html')) {
                unlink($file);
            }
        }

        return redirect()->to(base_url('settings?tab=general'))->with('success', 'Application cache cleared successfully.');
    }

    /**
     * Send a test SMS via the configured provider (AJAX endpoint)
     */
    public function testSms()
    {
        $json = $this->request->getJSON(true);
        $phone = $json['phone'] ?? '';

        if (empty($phone)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Phone number is required.']);
        }

        try {
            $smsService = new \App\Libraries\SmsService();

            if (!$smsService->isConfigured()) {
                $status = $smsService->getStatus();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $status['details'],
                ]);
            }

            $result = $smsService->sendTest($phone);

            return $this->response->setJSON([
                'success' => $result['success'],
                'message' => $result['success']
                    ? 'Test SMS sent successfully via ' . ucfirst($smsService->getProvider()) . '! Check your phone.'
                    : 'Failed: ' . ($result['error'] ?? 'Unknown error'),
                'provider' => $smsService->getProvider(),
            ]);
        } catch (\Exception $e) {
            log_message('error', '[TestSMS] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
