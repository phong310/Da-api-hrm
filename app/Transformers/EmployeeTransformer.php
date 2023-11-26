<?php

namespace App\Transformers;

use App\Models\Employee;
use App\Models\Master\Position;
use League\Fractal\TransformerAbstract;

class EmployeeTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Employee $em)
    {
        $res = [
            'id' => $em->id,
            'card_number' => $em->card_number,
            'employee_code' => $em->employee_code,
            'official_employee_date' => $em->official_employee_date,
            'date_start_work' => $em->date_start_work,
            'position_id' => $em->position_id,
            'department_id' => $em->department_id,
            'branch_id' => $em->branch_id,
            'personal_information_id' => $em->personal_information_id,
            'status' => $em->status,
            //            'information' => $em->information,
            'bankAccount' => $em->bankAccount,
            //            'position' => $em->position,
            //            'department' => $em->department,
            //            'branch' => $em->branch,
            'user' => $em->user,
        ];

        if ($em->information) {
            $res['personal_information']['id'] = $em->information->id;
            $res['personal_information']['full_name'] = $em->information->full_name;
            $res['personal_information']['email'] = $em->information->email;
            $res['personal_information']['birthday'] = $em->information->birthday;
            $res['personal_information']['marital_status'] = $em->information->marital_status;
            $res['personal_information']['sex'] = $em->information->sex;
            $res['personal_information']['phone'] = $em->information->phone;
            $res['personal_information']['avatar'] = $em->information->thumbnail_url;
            $res['personal_information']['addresses'] = $em->information->addresses;
        }

        if ($em->position) {
            $res['position']['name'] = $em->position->name;
            $res['position']['id'] = $em->position->id;
        }

        if ($em->department) {
            $res['department']['name'] = $em->department->name;
            $res['department']['id'] = $em->department->id;
        }

        if ($em->branch) {
            $res['branch']['name'] = $em->branch->name;
            $res['branch']['id'] = $em->branch->id;
        }

        return $res;
    }
}
