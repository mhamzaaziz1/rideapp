<?php

namespace Modules\Customer\Controllers;

use App\Controllers\BaseController;
use Modules\Customer\Models\CustomerAddressModel;

class CustomerAddressController extends BaseController
{
    protected $addressModel;

    public function __construct()
    {
        $this->addressModel = new CustomerAddressModel();
    }

    public function create()
    {
        $rules = [
            'customer_id' => 'required|integer',
            'type'        => 'required|max_length[50]',
            'address'     => 'required',
            'latitude'    => 'permit_empty|decimal',
            'longitude'   => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Invalid address data');
        }

        $data = $this->request->getPost();
        
        // Handle is_default
        $isDefault = $this->request->getPost('is_default') === 'on' ? 1 : 0;
        $data['is_default'] = $isDefault;

        // If this is the first address for the customer, force it to be default
        $existing = $this->addressModel->where('customer_id', $data['customer_id'])->countAllResults();
        if ($existing == 0) {
            $data['is_default'] = 1;
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if ($this->addressModel->insert($data)) {
                $addressId = $this->addressModel->getInsertID();

                if ($data['is_default']) {
                    $this->addressModel->unsetOtherDefaults($data['customer_id'], $addressId);
                }
            } else {
                throw new \Exception('Failed to insert address');
            }
            $db->transComplete();
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Failed to add address');
        }

        return redirect()->back()->with('success', 'Address added successfully');
    }

    public function update($id)
    {
        $address = $this->addressModel->find($id);
        if (!$address) {
            return redirect()->back()->with('error', 'Address not found');
        }

        $rules = [
            'type'        => 'required|max_length[50]',
            'address'     => 'required',
            'latitude'    => 'permit_empty|decimal',
            'longitude'   => 'permit_empty|decimal',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Invalid address data');
        }

        $data = $this->request->getPost();
        $isDefault = $this->request->getPost('is_default') === 'on' ? 1 : 0;
        $data['is_default'] = $isDefault;

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $this->addressModel->update($id, $data);

            if ($data['is_default']) {
                $this->addressModel->unsetOtherDefaults($address->customer_id, $id);
            }
            
            // If we unset default, we need to ensure at least one is default? 
            // Usually not strictly enforced unless business logic requires it.
            // But if we unset default, we might leave customer with no default.
            // Let's assume user action is explicit. 

            $db->transComplete();
        } catch (\Exception $e) {
             $db->transRollback();
             return redirect()->back()->withInput()->with('error', 'Failed to update address');
        }

        return redirect()->back()->with('success', 'Address updated successfully');
    }

    public function delete($id)
    {
        $address = $this->addressModel->find($id);
        if (!$address) {
            return redirect()->back()->with('error', 'Address not found');
        }

        if ($this->addressModel->delete($id)) {
            return redirect()->back()->with('success', 'Address deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete address');
    }

    public function setDefault($id)
    {
        $address = $this->addressModel->find($id);
        if (!$address) {
            return redirect()->back()->with('error', 'Address not found');
        }

        $db = \Config\Database::connect();
        $db->transStart();
        
        $this->addressModel->update($id, ['is_default' => 1]);
        $this->addressModel->unsetOtherDefaults($address->customer_id, $id);

        $db->transComplete();

        return redirect()->back()->with('success', 'Default address updated');
    }
}
