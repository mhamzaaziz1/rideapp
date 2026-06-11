<?php

namespace Modules\Dispatch\Controllers;

use App\Controllers\BaseController;
use Modules\Dispatch\Models\CommunicationLogModel;

class CommunicationController extends BaseController
{
    public function index()
    {
        $logModel = new CommunicationLogModel();
        
        // Fetch up to 500 recent communications
        $logs = $logModel->getLogsWithDetails(500, 0);
        
        $db = \Config\Database::connect();
        $testUsers = [];
        
        $customers = $db->table('customers')->select('id, first_name, last_name, phone')->get()->getResult();
        foreach($customers as $c) {
            if (!empty($c->phone)) {
                $testUsers[] = ['type' => 'Customer', 'name' => $c->first_name . ' ' . $c->last_name, 'phone' => $c->phone];
            }
        }
        
        $drivers = $db->table('drivers')->select('id, first_name, last_name, phone')->get()->getResult();
        foreach($drivers as $d) {
            if (!empty($d->phone)) {
                $testUsers[] = ['type' => 'Driver', 'name' => $d->first_name . ' ' . $d->last_name, 'phone' => $d->phone];
            }
        }

        $data = [
            'page_title' => 'Communication Center',
            'logs'       => $logs,
            'testUsers'  => $testUsers
        ];

        return view('Modules\Dispatch\Views\communications\index', $data);
    }
}
