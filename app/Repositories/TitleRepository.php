<?php

namespace App\Repositories;

use App\Models\Master\Title;
use App\Repositories\Interfaces\TitleInterface;

class TitleRepository implements TitleInterface
{
    protected $title;

    public function __construct(Title $title)
    {
        $this->title = $title;
    }

    public function getByCompanyId($company_id)
    {
        return $this->title::query()->where('company_id', $company_id)->get();
    }
}
