<?php

namespace Modules\Pricing\Models;

use CodeIgniter\Model;

class PricingZoneModel extends Model
{
    protected $table = 'pricing_zones';
    protected $primaryKey = 'id';
    protected $allowedFields = ['pricing_rule_id', 'name', 'description', 'price', 'is_active'];
    protected $useTimestamps = true;
    protected $updatedField  = null;
}
