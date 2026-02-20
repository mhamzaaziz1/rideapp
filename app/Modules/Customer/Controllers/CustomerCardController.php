<?php

namespace Modules\Customer\Controllers;

use App\Controllers\BaseController;
use Modules\Customer\Models\CustomerCardModel;

class CustomerCardController extends BaseController
{
    protected $cardModel;

    public function __construct()
    {
        $this->cardModel = new CustomerCardModel();
    }

    public function create()
    {
        $rules = [
            'customer_id'      => 'required|integer',
            'card_number'      => 'required|min_length[13]|max_length[19]',
            'expiry'           => 'required|regex_match[/\d{2}\/\d{2}/]', // MM/YY
            'cvv'              => 'required|min_length[3]|max_length[4]',
            'card_holder_name' => 'required|min_length[2]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Invalid card data');
        }

        $cardNumber = str_replace(' ', '', $this->request->getPost('card_number'));
        $expiry = explode('/', $this->request->getPost('expiry'));
        
        $brand = $this->detectCardBrand($cardNumber);
        $lastFour = substr($cardNumber, -4);
        $expMonth = $expiry[0];
        $expYear = '20' . $expiry[1]; // Assuming 21st century

        $isDefault = $this->request->getPost('is_default') === 'on' ? 1 : 0;
        $customerId = $this->request->getPost('customer_id');

        // Check if this is the first card for the customer
        $existingCount = $this->cardModel->where('customer_id', $customerId)->countAllResults();
        if ($existingCount == 0) {
            $isDefault = 1;
        }

        $data = [
            'customer_id'      => $customerId,
            'card_brand'       => $brand,
            'card_last_four'   => $lastFour,
            'expiry_month'     => $expMonth,
            'expiry_year'      => $expYear,
            'card_holder_name' => $this->request->getPost('card_holder_name'),
            'is_default'       => $isDefault,
        ];

        if ($this->cardModel->save($data)) {
            $cardId = $this->cardModel->getInsertID();
            if ($isDefault) {
                $this->cardModel->unsetOtherDefaults($customerId, $cardId);
            }
            return redirect()->back()->with('success', 'Card added successfully');
        }

        return redirect()->back()->with('error', 'Failed to add card');
    }

    public function delete($id)
    {
        $card = $this->cardModel->find($id);
        if (!$card) {
            return redirect()->back()->with('error', 'Card not found');
        }

        // If deleting default card, warn or handle? For now, just delete.
        if ($this->cardModel->delete($id)) {
            return redirect()->back()->with('success', 'Card deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete card');
    }

    public function setDefault($id)
    {
        $card = $this->cardModel->find($id);
        if (!$card) {
            return redirect()->back()->with('error', 'Card not found');
        }

        // Set this card as default
        $this->cardModel->update($id, ['is_default' => 1]);
        // Unset others
        $this->cardModel->unsetOtherDefaults($card->customer_id, $id);

        return redirect()->back()->with('success', 'Default payment method updated');
    }

    private function detectCardBrand($number)
    {
        if (preg_match('/^4/', $number)) return 'Visa';
        if (preg_match('/^5[1-5]/', $number)) return 'Mastercard';
        if (preg_match('/^3[47]/', $number)) return 'Amex';
        if (preg_match('/^6(?:011|5)/', $number)) return 'Discover';
        return 'Unknown';
    }
}
