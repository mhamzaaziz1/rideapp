<?php

namespace Modules\CallCenter\Entities;

use CodeIgniter\Entity\Entity;

class CallLog extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts   = [
        'duration' => 'integer',
    ];
}
