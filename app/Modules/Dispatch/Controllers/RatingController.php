<?php

namespace Modules\Dispatch\Controllers;

use App\Controllers\BaseController;
use Modules\Dispatch\Models\RatingModel;
use Modules\Dispatch\Models\TripModel;
use Modules\Fleet\Models\DriverModel;
use Modules\Customer\Models\CustomerModel;

class RatingController extends BaseController
{
    protected $ratingModel;
    protected $tripModel;
    protected $driverModel;
    protected $customerModel;

    public function __construct()
    {
        $this->ratingModel = new RatingModel();
        $this->tripModel = new TripModel();
        $this->driverModel = new DriverModel();
        $this->customerModel = new CustomerModel();
    }

    /**
     * Submit a rating for a trip
     * POST /dispatch/ratings/submit
     */
    public function submit()
    {
        // Simple validation
        $rules = [
            'trip_id'    => 'required|integer',
            'rater_type' => 'required|in_list[driver,customer]',
            'rater_id'   => 'required|integer', // In production, get from session
            'rating'     => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
            'comment'    => 'permit_empty|string|max_length[1000]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ])->setStatusCode(400); 
        }

        $tripId = $this->request->getPost('trip_id');
        $raterType = $this->request->getPost('rater_type');
        $raterId = $this->request->getPost('rater_id');
        $ratingScore = $this->request->getPost('rating');
        $comment = $this->request->getPost('comment');

        $trip = $this->tripModel->find($tripId);

        if (!$trip) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Trip not found'])->setStatusCode(404);
        }

        // Determine ratee and validate participation
        $rateeType = ($raterType === 'driver') ? 'customer' : 'driver';
        $rateeId = 0;

        if ($raterType === 'driver') {
            if ($trip->driver_id != $raterId) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Driver did not perform this trip'])->setStatusCode(403);
            }
            $rateeId = $trip->customer_id;
        } else {
            if ($trip->customer_id != $raterId) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Customer did not request this trip'])->setStatusCode(403);
            }
            $rateeId = $trip->driver_id;
        }

        // Check for existing rating
        $existing = $this->ratingModel->where([
            'trip_id'    => $tripId,
            'rater_type' => $raterType,
            'rater_id'   => $raterId
        ])->first();

        if ($existing) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'You have already rated this trip'])->setStatusCode(409);
        }

        // Save Rating
        $data = [
            'trip_id'    => $tripId,
            'rater_type' => $raterType,
            'rater_id'   => $raterId,
            'ratee_type' => $rateeType,
            'ratee_id'   => $rateeId,
            'rating'     => $ratingScore,
            'comment'    => $comment
        ];

        if ($this->ratingModel->save($data)) {
            // Update Average Rating for the Ratee
            $this->updateEntityRating($rateeType, $rateeId);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Rating submitted successfully']);
        }

        return $this->response->setJSON(['status' => 'error', 'errors' => $this->ratingModel->errors()])->setStatusCode(500);
    }

    /**
     * Get Ratings for a specific entity (Driver or Customer)
     * GET /dispatch/ratings/list?type=driver&id=1
     */
    public function list()
    {
        $type = $this->request->getGet('type');
        $id = $this->request->getGet('id');

        if (!in_array($type, ['driver', 'customer']) || empty($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid parameters'])->setStatusCode(400);
        }

        $ratings = $this->ratingModel->where('ratee_type', $type)
                                     ->where('ratee_id', $id)
                                     ->orderBy('created_at', 'DESC')
                                     ->findAll();

        return $this->response->setJSON(['status' => 'success', 'data' => $ratings]);
    }

    /**
     * Start the calculation logic
     */
    private function updateEntityRating($type, $id)
    {
        // Calculate new average
        // CodeIgniter 4 Model doesn't have direct avg() method on builder easily without getting result first or using selectAvg
        $builder = $this->ratingModel->builder();
        $builder->selectAvg('rating');
        $builder->where('ratee_type', $type);
        $builder->where('ratee_id', $id);
        $query = $builder->get();
        $row = $query->getRow();
        
        $average = $row->rating ?? 0;
        
        // Round to 1 decimal place
        $average = round($average, 1);

        // Update the entity table
        if ($type === 'driver') {
            $this->driverModel->update($id, ['rating' => $average]);
        } else {
            $this->customerModel->update($id, ['rating' => $average]);
        }
    }
}
