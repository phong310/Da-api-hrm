<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class SettingTypeOvertimeTransform extends TransformerAbstract
{
    public function transform($settingTypeOvertime): array
    {
        $data = [
            'id' => $settingTypeOvertime['id'],
            'company_id' => $settingTypeOvertime['company_id'],
            'type' => $settingTypeOvertime['type'],
            'setting_ot_salary_coefficients' => $settingTypeOvertime['settingOvertimeSalaryCoefficient']
        ];

        foreach ($data['setting_ot_salary_coefficients'] as $d) {
            unset($d['created_at']);
            unset($d['updated_at']);
        }

        return $data;
    }
}
