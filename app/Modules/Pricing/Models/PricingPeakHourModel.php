<?php

namespace Modules\Pricing\Models;

use CodeIgniter\Model;

class PricingPeakHourModel extends Model
{
    protected $table = 'pricing_peak_hours';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pricing_rule_id', 'day_of_week', 'start_time', 'end_time', 'multiplier'];
    protected $useTimestamps = true;
    protected $updatedField  = null; // Only created_at needed strictly
}
