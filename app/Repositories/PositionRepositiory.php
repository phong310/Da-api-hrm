<?php

namespace App\Repositories;

use App\Models\Master\Position;
use App\Repositories;
use App\Repositories\Interfaces\PositionInterface;

class PositionRepositiory implements PositionInterface
{
    protected $position;

    public function __construct(Position $position)
    {
        $this->position = $position;
    }

    public function getByCompanyId($company_id)
    {
        return $this->position::query()->where('company_id', $company_id)->get();
    }
}
