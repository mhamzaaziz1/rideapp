<?php

namespace Modules\Customer\Entities;

use CodeIgniter\Entity\Entity;

class Customer extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
        'rating' => 'double',
        'total_trips' => 'integer',
        'total_spent' => 'double',
    ];

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
