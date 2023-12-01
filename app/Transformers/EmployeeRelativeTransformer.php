<?php

namespace App\Transformers;

use App\Models\Relative;
use App\Models\Relatives;
use League\Fractal\TransformerAbstract;

class EmployeeRelativeTransformer extends TransformerAbstract
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
    public function transform(Relative $relatives)
    {
        $res = [
            'id' => $relatives->id,
            'first_name' => $relatives->first_name,
            'last_name' => $relatives->last_name,
            'birthday' => $relatives->birthday,
            'relationship_type' => $relatives->relationship_type,
            'ward' => $relatives->ward,
            'address' => $relatives->address,
            'district' => $relatives->district,
            'province' => $relatives->province,
            'phone' => $relatives->phone,
            'employee_id' => $relatives->employee_id,
            'sex' => $relatives->sex,
            'full_name' =>  $relatives->first_name . " " . $relatives->last_name,
            'full_address' => $relatives->address . ", " . $relatives->ward . ", " . $relatives->district . ", " . $relatives->province,
            'is_dependent_person' => $relatives->is_dependent_person,
            'date_apply' => $relatives->date_apply,
        ];

        return $res;
    }
}
