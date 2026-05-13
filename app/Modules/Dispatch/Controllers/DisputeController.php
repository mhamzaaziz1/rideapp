<?php

namespace Modules\Dispatch\Controllers;

use App\Controllers\BaseController;
use Modules\Dispatch\Models\DisputeModel;
use Modules\Dispatch\Models\TripModel;
use Modules\Billing\Services\WalletService;

class DisputeController extends BaseController
{
    public function __construct()
    {
        $this->disputeModel = new DisputeModel();
        $this->tripModel = new TripModel();
    }

    public function index()
    {
        $data['title'] = 'Disputes Management';
        $data['disputes'] = $this->disputeModel->getDisputesWithDetails();
        return view('Modules\Dispatch\Views\disputes\index', $data);
    }
    
    public function view($id)
    {
        $data['dispute'] = $this->disputeModel->select('disputes.*, t.trip_number, 
                                          c.first_name as c_first_name, c.last_name as c_last_name, c.phone as c_phone, c.rating as c_rating, c.wallet_balance as c_wallet_balance, c.total_trips as c_total_trips, c.avatar as c_avatar,
                                          d.first_name as d_first_name, d.last_name as d_last_name, d.phone as d_phone, d.rating as d_rating, d.wallet_balance as d_wallet_balance, d.total_trips as d_total_trips, d.avatar as d_avatar,
                                          u.first_name as admin_first_name, u.last_name as admin_last_name')
                                ->join('trips t', 't.id = disputes.trip_id', 'left')
                                ->join('customers c', 'c.id = disputes.customer_id', 'left')
                                ->join('drivers d', 'd.id = disputes.driver_id', 'left')
                                ->join('users u', 'u.id = disputes.resolved_by', 'left')
                                ->where('disputes.id', $id)
                                ->first();
                                
        if (!$data['dispute']) {
            return redirect()->to('/admin/disputes')->with('error', 'Dispute not found.');
        }

        $commentModel = new \Modules\Dispatch\Models\DisputeCommentModel();
        $data['comments'] = $commentModel->getCommentsWithUser($id);

        $data['title'] = 'View Dispute - ' . $data['dispute']->title;
        return view('Modules\Dispatch\Views\disputes\view', $data);
    }

    public function addComment($id)
    {
        $comment = $this->request->getPost('comment');

        if (empty(trim($comment))) {
            return redirect()->back()->with('error', 'Comment cannot be empty.');
        }

        $commentModel = new \Modules\Dispatch\Models\DisputeCommentModel();
        $commentModel->insert([
            'dispute_id' => $id,
            'user_id' => session()->get('user_id'),
            'comment' => trim($comment)
        ]);

        return redirect()->back()->with('success', 'Progress note added successfully.');
    }

    public function updateStatus($id)
    {
        $status = $this->request->getPost('status');
        $resolution = $this->request->getPost('resolution');

        // Validate legal transitions
        $validTransitions = [
            'open'          => ['investigating', 'resolved', 'closed'],
            'investigating' => ['resolved', 'closed', 'open'],
            'resolved'      => ['closed'],
            'closed'        => [],  // Terminal
        ];

        $dispute = $this->disputeModel->find($id);
        if (!$dispute) {
            return redirect()->back()->with('error', 'Dispute not found.');
        }

        $currentStatus = $dispute->status ?? 'open';
        $allowed = $validTransitions[$currentStatus] ?? [];

        if (!in_array($status, $allowed)) {
            return redirect()->back()->with('error', 'Invalid status transition: ' . ucfirst($currentStatus) . ' → ' . ucfirst($status));
        }

        $updateData = ['status' => $status];
        
        if ($resolution) {
            $updateData['resolution'] = $resolution;
        }

        if (in_array($status, ['resolved', 'closed'])) {
            $updateData['resolved_by'] = session()->get('user_id');
            $updateData['resolved_at'] = date('Y-m-d H:i:s');
        }

        if ($this->disputeModel->update($id, $updateData)) {
            return redirect()->back()->with('success', 'Dispute status updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update dispute status.');
    }

    public function edit($id)
    {
        $data['dispute'] = $this->disputeModel->find($id);
        if (!$data['dispute']) {
            return redirect()->to('/admin/disputes')->with('error', 'Dispute not found.');
        }

        $data['title'] = 'Edit Dispute - ' . $data['dispute']->title;
        return view('Modules\Dispatch\Views\disputes\edit', $data);
    }

    public function updateDetails($id)
    {
        $dispute = $this->disputeModel->find($id);
        if (!$dispute) {
            return redirect()->to('/admin/disputes')->with('error', 'Dispute not found.');
        }

        $updateData = [
            'title' => $this->request->getPost('title'),
            'description' => $this->request->getPost('description'),
            'dispute_type' => $this->request->getPost('dispute_type')
        ];

        if ($this->disputeModel->update($id, $updateData)) {
            return redirect()->to('/admin/disputes/view/' . $id)->with('success', 'Dispute updated successfully.');
        }

        return redirect()->back()->with('error', 'Failed to update dispute details.');
    }

    public function delete($id)
    {
        $dispute = $this->disputeModel->find($id);
        if (!$dispute) {
            return redirect()->to('/admin/disputes')->with('error', 'Dispute not found.');
        }

        // Prevent deleting resolved/closed disputes (audit trail)
        if (in_array($dispute->status, ['resolved', 'closed'])) {
            return redirect()->back()->with('error', 'Cannot delete a resolved or closed dispute. It is part of the financial audit trail.');
        }

        if ($this->disputeModel->delete($id)) {
            return redirect()->to('/admin/disputes')->with('success', 'Dispute deleted successfully.');
        }

        return redirect()->back()->with('error', 'Failed to delete dispute.');
    }

    public function arrangeReturnTrip($id)
    {
        $dispute = $this->disputeModel->find($id);
        if (!$dispute) {
            return redirect()->back()->with('error', 'Dispute not found.');
        }

        $originalTrip = $this->tripModel->find($dispute->trip_id);
        if (!$originalTrip) {
            return redirect()->back()->with('error', 'Original trip not found. Cannot arrange return trip.');
        }

        // Get form fields
        $tripNotes   = $this->request->getPost('trip_notes');
        $tripFare    = (float) ($this->request->getPost('trip_fare') ?? 0.00);
        $vehicleType = $this->request->getPost('vehicle_type') ?? $originalTrip->vehicle_type;

        // Address fields from the form (set by address autocomplete)
        $pickupAddress  = $this->request->getPost('pickup_address');
        $dropoffAddress = $this->request->getPost('dropoff_address');
        $pickupLat      = $this->request->getPost('pickup_lat');
        $pickupLng      = $this->request->getPost('pickup_lng');
        $dropoffLat     = $this->request->getPost('dropoff_lat');
        $dropoffLng     = $this->request->getPost('dropoff_lng');
        $distanceMiles  = (float) ($this->request->getPost('distance_miles') ?? $originalTrip->distance_miles);
        $durationMin    = (int)   ($this->request->getPost('duration_minutes') ?? $originalTrip->duration_minutes);

        // Fallback to reversed original trip coords if user didn't supply addresses
        if (empty($pickupAddress) || empty($dropoffAddress)) {
            $pickupAddress  = $originalTrip->dropoff_address;
            $dropoffAddress = $originalTrip->pickup_address;
            $pickupLat      = $originalTrip->dropoff_lat;
            $pickupLng      = $originalTrip->dropoff_lng;
            $dropoffLat     = $originalTrip->pickup_lat;
            $dropoffLng     = $originalTrip->pickup_lng;
            $distanceMiles  = $originalTrip->distance_miles;
            $durationMin    = $originalTrip->duration_minutes;
        }

        // Default note if not provided
        if (empty(trim((string) $tripNotes))) {
            if ($dispute->dispute_type == 'Lost Item') {
                $tripNotes = 'Lost Item Return Trip for Dispute #DSP-' . $dispute->id;
            } else {
                $tripNotes = 'Resolution Trip for Dispute #DSP-' . $dispute->id . ' (' . $dispute->dispute_type . '). Arranged by admin.';
            }
        }

        // Trip number prefix
        $prefix = ($dispute->dispute_type == 'Lost Item') ? 'RTN-' : 'RES-';

        $newTrip = [
            'trip_number'      => $prefix . strtoupper(bin2hex(random_bytes(4))),
            'customer_id'      => $originalTrip->customer_id,
            'driver_id'        => $this->request->getPost('driver_id') ?: $originalTrip->driver_id,
            'status'           => 'pending',
            'pickup_address'   => $pickupAddress,
            'dropoff_address'  => $dropoffAddress,
            'pickup_lat'       => $pickupLat,
            'pickup_lng'       => $pickupLng,
            'dropoff_lat'      => $dropoffLat,
            'dropoff_lng'      => $dropoffLng,
            'distance_miles'   => $distanceMiles,
            'duration_minutes' => $durationMin,
            'fare_amount'      => $tripFare,
            'surcharge_amount' => 0.00,
            'vehicle_type'     => $vehicleType,
            'passengers'       => 1,
            'notes'            => $tripNotes,
            'linked_dispute_id'=> $dispute->id,
        ];

        if ($this->tripModel->insert($newTrip)) {
            $newTripId = $this->tripModel->getInsertID();
            return redirect()->to('/dispatch/trips/view/' . $newTripId)->with('success', 'Resolution trip dispatched successfully.');
        }

        return redirect()->back()->with('error', 'Failed to arrange return trip.');
    }

    public function settleFare($id)
    {
        $dispute = $this->disputeModel->find($id);
        if (!$dispute) {
            return redirect()->back()->with('error', 'Dispute not found.');
        }

        if ($dispute->status == 'resolved' || $dispute->status == 'closed') {
            return redirect()->back()->with('error', 'Dispute is already resolved and settled.');
        }

        $settleTo = $this->request->getPost('settle_to');
        $amount = (float) $this->request->getPost('amount');
        $notes = $this->request->getPost('notes');

        if ($amount <= 0) {
            return redirect()->back()->with('error', 'Invalid settlement amount.');
        }

        // ── ATOMIC TRANSACTION ──────────────────────────────────────
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $walletTxModel = new \Modules\Billing\Models\WalletTransactionModel();
            $customerModel = new \Modules\Customer\Models\CustomerModel();
            $driverModel = new \Modules\Fleet\Models\DriverModel();

            if ($settleTo == 'customer' && !empty($dispute->customer_id)) {
                $walletTxModel->insert([
                    'user_type' => 'customer',
                    'user_id' => $dispute->customer_id,
                    'type' => 'deposit',
                    'amount' => $amount,
                    'description' => 'Dispute Settlement Refund DSP-' . $dispute->id . ': ' . $notes
                ]);
            } elseif ($settleTo == 'driver' && !empty($dispute->driver_id)) {
                $walletTxModel->insert([
                    'user_type' => 'driver',
                    'user_id' => $dispute->driver_id,
                    'type' => 'deposit',
                    'amount' => $amount,
                    'description' => 'Dispute Settlement Payout DSP-' . $dispute->id . ': ' . $notes
                ]);
            } elseif ($settleTo == 'transfer_to_customer' && !empty($dispute->customer_id) && !empty($dispute->driver_id)) {
                $walletTxModel->insert([
                    'user_type' => 'driver',
                    'user_id' => $dispute->driver_id,
                    'type' => 'withdrawal',
                    'amount' => $amount,
                    'description' => 'Dispute Fare Deduction DSP-' . $dispute->id . ': ' . $notes
                ]);
                $walletTxModel->insert([
                    'user_type' => 'customer',
                    'user_id' => $dispute->customer_id,
                    'type' => 'deposit',
                    'amount' => $amount,
                    'description' => 'Dispute Fare Transfer Refund DSP-' . $dispute->id . ': ' . $notes
                ]);
            } elseif ($settleTo == 'transfer_to_driver' && !empty($dispute->customer_id) && !empty($dispute->driver_id)) {
                $walletTxModel->insert([
                    'user_type' => 'customer',
                    'user_id' => $dispute->customer_id,
                    'type' => 'withdrawal',
                    'amount' => $amount,
                    'description' => 'Dispute Fare Deduction DSP-' . $dispute->id . ': ' . $notes
                ]);
                $walletTxModel->insert([
                    'user_type' => 'driver',
                    'user_id' => $dispute->driver_id,
                    'type' => 'deposit',
                    'amount' => $amount,
                    'description' => 'Dispute Fare Transfer Payout DSP-' . $dispute->id . ': ' . $notes
                ]);
            } else {
                $db->transRollback();
                return redirect()->back()->with('error', 'Invalid beneficiary or user missing.');
            }

            $settleText = $settleTo;
            if ($settleTo == 'transfer_to_customer') {
                $settleText = 'Customer (Deducted from driver)';
            } elseif ($settleTo == 'transfer_to_driver') {
                $settleText = 'Driver (Deducted from customer)';
            }

            // Resolve the dispute
            $this->disputeModel->update($id, [
                'status' => 'resolved',
                'resolution' => "Settled \${$amount} payout to {$settleText}.\nNotes: {$notes}",
                'resolved_by' => session()->get('user_id'),
                'resolved_at' => date('Y-m-d H:i:s'),
            ]);

            $db->transCommit();

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[DisputeSettlement] ' . $e->getMessage());
            return redirect()->back()->with('error', 'Settlement failed: ' . $e->getMessage());
        }

        // Sync wallet balances AFTER the transaction is committed
        // This recomputes from wallet_transactions, ensuring consistency
        if (!empty($dispute->customer_id) && in_array($settleTo, ['customer', 'transfer_to_customer', 'transfer_to_driver'])) {
            WalletService::syncBalance('customer', (int)$dispute->customer_id);
        }
        if (!empty($dispute->driver_id) && in_array($settleTo, ['driver', 'transfer_to_customer', 'transfer_to_driver'])) {
            $driver = (new \Modules\Fleet\Models\DriverModel())->find($dispute->driver_id);
            WalletService::syncBalance('driver', (int)$dispute->driver_id, (float)($driver->commission_rate ?? 25.00));
        }

        return redirect()->back()->with('success', 'Fare settled successfully.');
    }

    // Sanitized API — only accept whitelisted fields
    public function apiCreate()
    {
        // WHITELIST: only accept these fields from the client
        $allowed = ['trip_id', 'customer_id', 'driver_id', 'reported_by', 'dispute_type', 'title', 'description'];
        $rawData = $this->request->getPost();
        $data = array_intersect_key($rawData, array_flip($allowed));

        // Handle file upload
        $file = $this->request->getFile('attachment');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            // Validate file type and size
            if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid file type. Allowed: JPEG, PNG, WebP, PDF'])->setStatusCode(400);
            }
            if ($file->getSize() > 5 * 1024 * 1024) { // 5MB max
                return $this->response->setJSON(['status' => 'error', 'message' => 'File too large. Maximum 5MB.'])->setStatusCode(400);
            }
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'assets/uploads/disputes', $newName);
            $data['attachment'] = 'assets/uploads/disputes/' . $newName;
        }

        // Force status to 'open' — never trust client for this
        $data['status'] = 'open';

        if (!$this->disputeModel->save($data)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->disputeModel->errors()
            ])->setStatusCode(400);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Dispute reported successfully',
            'data' => [
                'dispute_id' => $this->disputeModel->getInsertID(),
                'attachment_path' => isset($data['attachment']) ? base_url($data['attachment']) : null
            ]
        ])->setStatusCode(201);
    }
}
