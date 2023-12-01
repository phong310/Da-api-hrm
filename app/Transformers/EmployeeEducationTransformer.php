<?php

namespace App\Transformers;

use App\Models\Education;
use League\Fractal\TransformerAbstract;

class EmployeeEducationTransformer extends TransformerAbstract
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
    public function transform(Education $edu)
    {
        $res = [
            'id' => $edu->id,
            'personal_information_id' => $edu->personal_information_id,
            'school_name' => $edu->school_name,
            'description' => $edu->description,
            'from_date' => $edu->from_date,
            'to_date' => $edu->to_date,
        ];

        return $res;
    }
}
