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
        
        $data = [
            'page_title' => 'Communication Center',
            'logs'       => $logs
        ];

        return view('Modules\Dispatch\Views\communications\index', $data);
    }
}
