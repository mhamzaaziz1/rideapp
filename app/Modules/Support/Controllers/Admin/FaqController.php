<?php

namespace App\Modules\Support\Controllers\Admin;

use App\Controllers\BaseController;
use App\Modules\Support\Models\FaqModel;

class FaqController extends BaseController
{
    protected $faqModel;

    public function __construct()
    {
        $this->faqModel = new FaqModel();
    }

    public function index()
    {
        $data = [
            'title' => 'FAQ Management',
            'faqs' => $this->faqModel->orderBy('id', 'DESC')->findAll()
        ];

        return view('App\Modules\Support\Views\admin\faq\index', $data);
    }

    public function store()
    {
        $id = $this->request->getPost('id');
        $data = [
            'question_keyword' => $this->request->getPost('question_keyword'),
            'answer' => $this->request->getPost('answer'),
            'category' => $this->request->getPost('category') ?? 'general',
        ];

        if ($id) {
            $this->faqModel->update($id, $data);
            $msg = 'FAQ updated successfully';
        } else {
            $this->faqModel->insert($data);
            $msg = 'FAQ added successfully';
        }

        return redirect()->to(base_url('admin/support/faq'))->with('success', $msg);
    }

    public function delete($id)
    {
        $this->faqModel->delete($id);
        return redirect()->to(base_url('admin/support/faq'))->with('success', 'FAQ deleted successfully');
    }
}
