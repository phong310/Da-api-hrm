<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Setting\SettingSalaryTaxCoefficient;
use App\Repositories\Interfaces\SettingSalaryTaxCoefficientInterface;
use App\Transformers\SettingSalaryTaxCoefficientTransform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SettingSalaryTaxCoefficientService extends BaseService
{
    protected $settingSalaryTaxCoefficient;
    protected $modelHasSettingSalaryTaxCoefficient;


    public function __construct(SettingSalaryTaxCoefficientInterface $settingSalaryTaxCoefficient)
    {
        $this->settingSalaryTaxCoefficient = $settingSalaryTaxCoefficient;
        parent::__construct();
    }

    public function setModel()
    {
        $this->model = new SettingSalaryTaxCoefficient();
    }

    public function setTransformersData($data)
    {
        return collect([$data])->transformWith(new SettingSalaryTaxCoefficientTransform());
    }



    public function showSettingCoefficientByCompany()
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        $data = $this->settingSalaryTaxCoefficient->showSettingCoefficientByCompany($companyId);
        if (!$data) {
            return response()->json([
                'message' => 'Not found',
            ], 404);
        }

        return $this->setTransformersData($data);
    }

    public function update(Request $request)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();
            $settingSR = $this->settingSalaryTaxCoefficient->update($data, $data['id']);
            DB::commit();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $settingSR,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
