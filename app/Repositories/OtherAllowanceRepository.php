<?php

namespace App\Repositories;

use App\Models\OtherAllowance;
use App\Repositories\Interfaces\OtherAllowanceInterface;
use Illuminate\Support\Facades\Auth;

class OtherAllowanceRepository implements OtherAllowanceInterface
{

    /**
     * @var OtherAllowance
     */
    protected $other_allowance;

    public function __construct(OtherAllowance $other_allowance)
    {
        $this->other_allowance = $other_allowance;
    }

    public function show($Id)
    {
        return $this->other_allowance::query()->where(['id' => $Id])->first();
    }


    public function store($newData)
    {
        return $this->other_allowance::query()->create($newData);
    }

    // public function update($id, $data)
    // {
    //     $companyId = Auth::user()->company_id;
    //     $updatedData = [
    //         'company_id' => $companyId,
    //         'name' => $data['name']
    //     ];

    //     $allowance = $this->other_allowance::query()->findOrFail($id);
    //     $allowance->update($updatedData);

    //     return $allowance;
    // }

    public function update($other_allowance, $data)
    {
        $other_allowance->update($data);
        return $other_allowance;
    }
}
