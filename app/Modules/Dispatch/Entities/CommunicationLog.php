<?php

namespace Modules\Dispatch\Entities;

use CodeIgniter\Entity\Entity;

class CommunicationLog extends Entity
{
    protected $datamap = [];
    protected $dates   = ['created_at'];
    protected $casts   = [
        'id'        => 'integer',
        'user_id'   => 'integer',
    ];
}
