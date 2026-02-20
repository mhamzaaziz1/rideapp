<?php

namespace Modules\Pricing\Models;

use CodeIgniter\Model;

class PricingRuleModel extends Model
{
    protected $table = 'pricing_rules';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vehicle_type', 'base_fare', 'distance_rate_per_mile', 'time_rate_per_minute', 'minimum_fare'];
    protected $useTimestamps = true;
}
