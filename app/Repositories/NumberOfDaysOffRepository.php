<?php

namespace App\Repositories;

use App\Models\Form\NumberOfDaysOff;
use App\Repositories\Interfaces\NumberOfDaysOffInterface;

class NumberOfDaysOffRepository implements NumberOfDaysOffInterface
{
    /**
     * @var NumberOfDaysOff
     */
    private $numberOfDaysOff;

    public function __construct(NumberOfDaysOff $numberOfDaysOff)
    {
        $this->numberOfDaysOff = $numberOfDaysOff;
    }

    /**
     * @param $data
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     */
    public function create($data)
    {
        return $this->numberOfDaysOff::query()->create($data);
    }

    /**
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function show($id)
    {
        return $this->numberOfDaysOff::query()->where(['id' => $id])->first();
    }

    /**
     * @param $data
     * @param $id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function update($data, $id)
    {
        // TODO: Implement update() method.
        $numberOfDaysOff = $this->show($id);

        if (!$numberOfDaysOff) {
            return null;
        }

        $numberOfDaysOff->fill($data);
        $numberOfDaysOff->save();

        return $numberOfDaysOff;
    }
}
