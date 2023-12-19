<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Setting\SettingOvertimeSalaryCoefficient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class SettingOvertimeSalaryCoefficientService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new SettingOvertimeSalaryCoefficient();
    }

    /**
     * @param $data
     * @return Builder|Model
     */
    public function store($data)
    {
        return $this->model->query()->create($data);
    }

    public function storeArray($settingTypeOvertimeId, $data): \Illuminate\Http\JsonResponse
    {
        $company_id = Auth::user()->company_id;
        $tmp = [
            'setting_type_ot_id' => $settingTypeOvertimeId,
            'company_id' => $company_id
        ];

        foreach ($data as $d) {
            $this->store(array_merge($d, $tmp));
        }

        return response()->json([
            'message' => __('message.create_success'),
        ], 200);
    }

    /**
     * @param $data
     * @param $id
     * @return Builder|Model|object
     */

    public function update($data, $id)
    {
        $settingOT = $this->model->query()->where('id', $id)->first();

        if (!$settingOT) {
            return null;
        }

        $settingOT->fill($data);
        $settingOT->save();

        return $settingOT;
    }

    public function updateOrCreate($compareData, $data)
    {
        return $this->model->query()->updateOrCreate($compareData, $data);
    }

    public function updateArray($settingTypeOvertimeId, $newData)
    {
        $oldData = $this->model->query()->where('setting_type_ot_id', $settingTypeOvertimeId)->get();
        $newId = Arr::pluck($newData, 'id');

        $tmp = [
            'setting_type_ot_id' => $settingTypeOvertimeId,
            'company_id' => Auth::user()->company_id
        ];

        foreach ($newData as $d) {
            if (!isset($d['id'])) {
                $this->store(array_merge($d, $tmp));
            } else {
                $this->update($d, $d['id']);
            }
        }

        foreach ($oldData as $d) {
            if (!in_array($d['id'], $newId)) {
                $d->delete();
            }
        }
    }
}
