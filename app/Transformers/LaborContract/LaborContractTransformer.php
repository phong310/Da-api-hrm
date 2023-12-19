<?php

namespace App\Transformers\LaborContract;

use App\Models\LaborContract\LaborContract;
use League\Fractal\TransformerAbstract;
use App\Models\Employee;

class LaborContractTransformer extends TransformerAbstract
{
    public function transform(LaborContract $laborContract)
    {
        $data = $laborContract->toArray();
        $data['labor_contract_type'] = $laborContract->labor_contract_type;

        if ($data['employee']) {
            $employee = $data['employee'];
            $full_name = null;
            $branch_name = null;
            $position_name = null;
            $thumbnail_url = null;

            if ($employee['personal_information']) {
                $full_name = $employee['personal_information']['full_name'];
                $thumbnail_url = $employee['personal_information']['thumbnail_url'];
            }

            // Lấy thông tin branch và position từ Employee model
            $employeeModel = Employee::find($employee['id']);
            if ($employeeModel) {
                $branch = $employeeModel->branch;
                $position = $employeeModel->position;

                unset($data['employee']); // Xóa toàn bộ thông tin cũ

                $data['employee']['employee_code'] = $employee['employee_code'];
                $data['employee']['personal_information']['full_name'] = $full_name;
                $data['employee']['personal_information']['thumbnail_url'] = $thumbnail_url;

                // Thêm thông tin branch và position nếu có dữ liệu
                if ($branch) {
                    $branch_name = $branch->name;
                }

                if ($position) {
                    $position_name = $position->name;
                }
            }

            // Sử dụng thông tin branch và position từ employee nếu trong labor_contract là null
            if (is_null($data['position'])) {
                $data['position'] = ['name' => $position_name];
            }

            if (is_null($data['branch'])) {
                $data['branch'] = ['name' => $branch_name];
            }
        }

        return $data;
    }
}
