<?php

namespace Modules\CallCenter\Controllers;

use App\Controllers\BaseController;
use Modules\CallCenter\Models\CallLogModel;

class CallLogsController extends BaseController
{
    protected $callLogModel;

    public function __construct()
    {
        $this->callLogModel = new CallLogModel();
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        
        $query = $this->callLogModel->orderBy('created_at', 'DESC');
        
        if ($search) {
            $query->groupStart()
                  ->like('caller_name', $search)
                  ->orLike('caller_number', $search)
                  ->groupEnd();
        }

        $totalCalls = $this->callLogModel->countAllResults();
        $inboundCalls = $this->callLogModel->where('direction', 'inbound')->countAllResults();
        $outboundCalls = $this->callLogModel->where('direction', 'outbound')->countAllResults();

        $data = [
            'logs' => $query->paginate(20),
            'pager' => $this->callLogModel->pager,
            'search' => $search,
            'title' => 'Call Logs',
            'stats' => [
                'total' => $totalCalls,
                'inbound' => $inboundCalls,
                'outbound' => $outboundCalls
            ]
        ];

        return view('Modules\CallCenter\Views\index', $data);
    }

    public function create()
    {
        return view('Modules\CallCenter\Views\form', ['title' => 'Log New Call']);
    }

    public function store()
    {
        $rules = [
            'caller_number' => 'required',
            'status' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $this->callLogModel->save([
            'caller_name'   => $this->request->getPost('caller_name'),
            'caller_number' => $this->request->getPost('caller_number'),
            'direction'     => $this->request->getPost('direction'),
            'status'        => $this->request->getPost('status'),
            'duration'      => $this->request->getPost('duration'),
            'notes'         => $this->request->getPost('notes'),
        ]);

        return redirect()->to(base_url('call-logs'))->with('success', 'Call logged successfully.');
    }

    public function edit($id)
    {
        $call = $this->callLogModel->find($id);
        if (!$call) {
            return redirect()->back()->with('error', 'Call log not found.');
        }
        return view('Modules\CallCenter\Views\form', ['call' => $call, 'title' => 'Edit Call Log']);
    }

    public function update($id)
    {
        $call = $this->callLogModel->find($id);
        if (!$call) {
            return redirect()->back()->with('error', 'Call log not found.');
        }

        $this->callLogModel->update($id, [
            'caller_name'   => $this->request->getPost('caller_name'),
            'caller_number' => $this->request->getPost('caller_number'),
            'direction'     => $this->request->getPost('direction'),
            'status'        => $this->request->getPost('status'),
            'duration'      => $this->request->getPost('duration'),
            'notes'         => $this->request->getPost('notes'),
        ]);

        return redirect()->to(base_url('call-logs'))->with('success', 'Call log updated.');
    }

    public function delete($id)
    {
        $this->callLogModel->delete($id);
        return redirect()->to(base_url('call-logs'))->with('success', 'Call log deleted.');
    }
}
