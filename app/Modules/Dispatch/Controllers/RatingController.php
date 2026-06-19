<?php

namespace App\Modules\Dispatch\Controllers;

use App\Controllers\BaseController;
use App\Modules\Dispatch\Models\RatingModel;
use App\Modules\Dispatch\Models\TripModel;
use App\Modules\Fleet\Models\DriverModel;
use App\Modules\Customer\Models\CustomerModel;

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
        // Unified data source: try JSON first, fall back to POST
        try {
            $json = $this->request->getJSON(true);
        } catch (\Exception $e) {
            $json = null;
        }

        $data = $json ?? $this->request->getPost();

        // ── MANUAL VALIDATION (fixes JSON body bypass) ─────────────
        $errors = [];

        $tripId = filter_var($data['trip_id'] ?? null, FILTER_VALIDATE_INT);
        if (!$tripId) {
            $errors['trip_id'] = 'Trip ID is required and must be an integer.';
        }

        $raterType = $data['rater_type'] ?? '';
        if (!in_array($raterType, ['driver', 'customer', 'system'])) {
            $errors['rater_type'] = 'Rater type must be one of: driver, customer, system.';
        }

        $rateeType = $data['ratee_type'] ?? null;
        if ($rateeType !== null && !in_array($rateeType, ['driver', 'customer', ''])) {
            $errors['ratee_type'] = 'Ratee type must be driver or customer.';
        }

        $rateeId = isset($data['ratee_id']) ? filter_var($data['ratee_id'], FILTER_VALIDATE_INT) : null;
        $raterId = isset($data['rater_id']) ? filter_var($data['rater_id'], FILTER_VALIDATE_INT) : 0;

        $ratingScore = filter_var($data['rating'] ?? null, FILTER_VALIDATE_INT);
        if (!$ratingScore || $ratingScore < 1 || $ratingScore > 5) {
            $errors['rating'] = 'Rating must be an integer between 1 and 5.';
        }

        $comment = trim($data['comment'] ?? '');
        if (strlen($comment) > 1000) {
            $errors['comment'] = 'Comment must be 1000 characters or less.';
        }

        if (!empty($errors)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $errors
            ])->setStatusCode(400);
        }

        // ── TRIP VALIDATION ────────────────────────────────────────
        if ($raterId == 0) { $raterType = 'system'; }

        $trip = $this->tripModel->find($tripId);
        if (!$trip) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Trip not found'])->setStatusCode(404);
        }

        // Only allow rating completed trips
        if (strtolower($trip->status) !== 'completed') {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Ratings can only be submitted for completed trips. Current status: ' . ucfirst($trip->status)
            ])->setStatusCode(422);
        }

        // ── TIME WINDOW: 72 hours for non-system ratings ───────────
        if ($raterType !== 'system') {
            $completedAt = $trip->completed_at ?? $trip->updated_at ?? $trip->created_at;
            if ($completedAt) {
                $deadline = strtotime($completedAt) + (72 * 3600); // 72 hours
                if (time() > $deadline) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Rating window has expired. Ratings must be submitted within 72 hours of trip completion.'
                    ])->setStatusCode(422);
                }
            }
        }

        // ── RATEE RESOLUTION ───────────────────────────────────────
        if ($raterId == 0) {
            // Dispatch board / system rating
            if (empty($rateeType)) {
                $rateeType = ($raterType === 'driver') ? 'customer' : 'driver';
            }
            if (empty($rateeId)) {
                $rateeId = ($rateeType === 'driver') ? $trip->driver_id : $trip->customer_id;
            }
        } else {
            // Self-service rating: verify the rater participated in this trip
            $rateeType = ($raterType === 'driver') ? 'customer' : 'driver';
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
        }

        if (empty($rateeId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Cannot determine ratee. Trip may be missing a driver or customer.'])->setStatusCode(400);
        }

        // ── DUPLICATE CHECK ────────────────────────────────────────
        $existing = $this->ratingModel->where([
            'trip_id'    => $tripId,
            'rater_type' => $raterType,
            'rater_id'   => $raterId,
            'ratee_type' => $rateeType,
            'ratee_id'   => $rateeId
        ])->first();

        if ($existing) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'This participant has already been rated for this trip.'])->setStatusCode(409);
        }

        // ── SAVE RATING ────────────────────────────────────────────
        $insertData = [
            'trip_id'    => $tripId,
            'rater_type' => $raterType,
            'rater_id'   => $raterId,
            'ratee_type' => $rateeType,
            'ratee_id'   => $rateeId,
            'rating'     => $ratingScore,
            'comment'    => $comment
        ];

        if ($this->ratingModel->save($insertData)) {
            // Update Average Rating for the Ratee
            $this->updateEntityRating($rateeType, $rateeId);

            return $this->response->setJSON(['status' => 'success', 'message' => 'Rating submitted successfully']);
        }

        return $this->response->setJSON(['status' => 'error', 'errors' => $this->ratingModel->errors()])->setStatusCode(500);
    }

    /**
     * Get Ratings for a specific entity (Driver or Customer)
     * GET /dispatch/ratings/list?type=driver&id=1&page=1&limit=20
     */
    public function list()
    {
        $type = $this->request->getGet('type');
        $id = $this->request->getGet('id');
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $limit = min(50, max(1, (int) ($this->request->getGet('limit') ?? 20)));

        if (!in_array($type, ['driver', 'customer']) || empty($id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid parameters'])->setStatusCode(400);
        }

        $id = (int) $id;
        $offset = ($page - 1) * $limit;

        // Total count
        $total = $this->ratingModel->where('ratee_type', $type)->where('ratee_id', $id)->countAllResults(false);

        // Paginated ratings
        $ratings = $this->ratingModel->where('ratee_type', $type)
                                     ->where('ratee_id', $id)
                                     ->orderBy('created_at', 'DESC')
                                     ->findAll($limit, $offset);

        // Average
        $builder = $this->ratingModel->builder();
        $builder->selectAvg('rating', 'avg_rating');
        $builder->selectCount('rating', 'total_ratings');
        $builder->where('ratee_type', $type);
        $builder->where('ratee_id', $id);
        $stats = $builder->get()->getRow();

        return $this->response->setJSON([
            'status' => 'success',
            'data' => $ratings,
            'meta' => [
                'page'          => $page,
                'limit'         => $limit,
                'total'         => $total,
                'pages'         => ceil($total / $limit),
                'average'       => round((float) ($stats->avg_rating ?? 0), 1),
                'total_ratings' => (int) ($stats->total_ratings ?? 0),
            ]
        ]);
    }

    /**
     * Calculate and persist the average rating for an entity.
     * 
     * P2 #7: System ratings are weighted separately to prevent admin manipulation.
     * Formula: 90% user average + 10% system average (if both exist)
     * If only one type exists, that type is used at 100%.
     */
    private function updateEntityRating(string $type, int $id): void
    {
        $db = \Config\Database::connect();

        // User ratings (driver + customer raters)
        $userStats = $db->query(
            "SELECT AVG(rating) as avg_rating, COUNT(*) as cnt
             FROM ratings
             WHERE ratee_type = ? AND ratee_id = ? AND rater_type != 'system'",
            [$type, $id]
        )->getRow();

        // System ratings
        $sysStats = $db->query(
            "SELECT AVG(rating) as avg_rating, COUNT(*) as cnt
             FROM ratings
             WHERE ratee_type = ? AND ratee_id = ? AND rater_type = 'system'",
            [$type, $id]
        )->getRow();

        $userAvg = (float) ($userStats->avg_rating ?? 0);
        $userCnt = (int) ($userStats->cnt ?? 0);
        $sysAvg  = (float) ($sysStats->avg_rating ?? 0);
        $sysCnt  = (int) ($sysStats->cnt ?? 0);

        // Weighted blend: prioritize user ratings
        if ($userCnt > 0 && $sysCnt > 0) {
            $average = round(($userAvg * 0.9) + ($sysAvg * 0.1), 1);
        } elseif ($userCnt > 0) {
            $average = round($userAvg, 1);
        } elseif ($sysCnt > 0) {
            $average = round($sysAvg, 1);
        } else {
            $average = 0.0;
        }

        // Update the entity table
        if ($type === 'driver') {
            $this->driverModel->update($id, ['rating' => $average]);
        } else {
            $this->customerModel->update($id, ['rating' => $average]);
        }
    }
}
