<?php

namespace App\Transformers;

use App\Models\Form\NumberOfDaysOff;
use League\Fractal\TransformerAbstract;

class NumberOfDaysOffTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include.
     *
     * @var array
     */
    protected array $defaultIncludes = [];

    /**
     * List of resources possible to include.
     *
     * @var array
     */
    protected array $availableIncludes = [];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(NumberOfDaysOff $timeOff)
    {
        $res = [
            'id' => $timeOff->id,
            'date' => $timeOff->date,
            'employee_id' => $timeOff->employee_id,
            'number_of_minutes' => $timeOff->number_of_minutes,
            'type' => $timeOff->type,
        ];

        return $res;
    }
}
