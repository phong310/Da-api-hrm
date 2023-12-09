<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Form\ModelHasApprovers;
use Illuminate\Http\Request;

class ModelHasApproversService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new ModelHasApprovers();
    }

    /**
     * @param Request $request
     * @param $form
     * @return array
     */
    public function store(Request $request, $form)
    {
        $approvers = [];
        if ($request->approver_id_1) {
            $approvers[] = $request->approver_id_1;
        }
        if ($request->approver_id_2) {
            $approvers[] = $request->approver_id_2;
        }
        $modelHasApprovers = [];
        foreach ($approvers as $approver) {
            $modelHasApprover = ModelHasApprovers::create([
                'approve_employee_id' => $approver,
                'status' => $form->status,
                'model_type' => get_class($form),
                'model_id' => $form->id,
            ]);
            $modelHasApprovers[] = $modelHasApprover;
        }

        return $modelHasApprovers;
    }

    /**
     * @param Request $request
     * @param $form
     * @return mixed
     */
    public function update(Request $request, $form)
    {
        $newApprovers = [];
        if ($request->approver_id_1) {
            $newApprovers[] = [
                'model_id' => $form->id,
                'model_type' => get_class($form),
                'approve_employee_id' => $request->approver_id_1,
            ];
        }
        if ($request->approver_id_2) {
            $newApprovers[] = [
                'model_id' => $form->id,
                'model_type' => get_class($form),
                'approve_employee_id' => $request->approver_id_2,
            ];
        }

        $approvers = $this->syncData($newApprovers, $form);

        return $approvers;
    }

    /**
     * @param $model_id
     * @param $approve_id
     * @param $model_type
     * @param $status
     */
    public function updateStatus($model_id, $approve_id, $model_type, $status)
    {
        ModelHasApprovers::query()->where([
            'model_id' => $model_id,
            'approve_employee_id' => $approve_id,
            'model_type' => $model_type,
        ])->update(['status' => $status, ModelHasApprovers::ACTION_TIME[$status] => now()]);
    }

    public function syncData($newData, $form)
    {
        $approvers = [];
        $form->approvers()->delete();
        foreach ($newData as $d) {
            $data = [
                'model_id' => $d['model_id'],
                'model_type' => $d['model_type'],
                'approve_employee_id' => $d['approve_employee_id'],
            ];
            $approvers[] = ModelHasApprovers::query()->create($data);
        }

        return $approvers;
    }
}
