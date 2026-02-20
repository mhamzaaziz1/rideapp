<?php

namespace Modules\Fleet\Entities;

use CodeIgniter\Entity\Entity;

class Driver extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
        'total_trips' => 'integer',
        'rating'      => 'double',
    ];

    public function getFullName()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
