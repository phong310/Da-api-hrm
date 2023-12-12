<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Setting\SettingTypeOvertime;
use App\Repositories\Interfaces\SettingTypesOvertimeInterface;
use App\Transformers\SettingTypeOvertimeTransform;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class SettingTypeOvertimeService extends BaseService
{
    /**
     * @var SettingTypesOvertimeInterface
     */
    protected $settingTypesOvertime;
    /**
     * @var SettingOvertimeSalaryCoefficientService
     */
    protected $overtimeSalaryCoefficientService;

    /**
     * @param SettingTypesOvertimeInterface $settingTypesOvertime
     */
    public function __construct(
        SettingTypesOvertimeInterface           $settingTypesOvertime,
        // SettingOvertimeSalaryCoefficientService $overtimeSalaryCoefficientService
    ) {
        $this->settingTypesOvertime = $settingTypesOvertime;
        // $this->overtimeSalaryCoefficientService = $overtimeSalaryCoefficientService;
        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new SettingTypeOvertime();
    }

    public function setTransformersNotPaginate($data)
    {
        $transform = collect([$data])->transformWith(new SettingTypeOvertimeTransform())->toArray();
        return $transform['data'][0];
    }

    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $settingTypeOvertime = collect($collection)->transformWith(new SettingTypeOvertimeTransform())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $settingTypeOvertime;
    }

    public function appendFilter()
    {
        $this->query->with(['settingOvertimeSalaryCoefficient']);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $user = Auth::user();
        $data['company_id'] = $user->company_id;

        try {
            DB::beginTransaction();
            $settingTO = $this->settingTypesOvertime->store($data);
            $this->overtimeSalaryCoefficientService->storeArray($settingTO['id'], $data['setting_ot_salary_coefficients']);
            DB::commit();

            return response()->json([
                'message' => __('message.create_success'),
                'data' => $settingTO,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @param $type
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function showByType($type)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        $data = $this->settingTypesOvertime->showByType($companyId, $type);

        if (!$data) {
            return response()->json([
                'message' => 'Not found',
            ], 404);
        }

        return $this->setTransformersNotPaginate($data);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        $data = $request->all();
        try {
            DB::beginTransaction();
            $this->overtimeSalaryCoefficientService->updateArray($data['id'], $data['setting_ot_salary_coefficients']);
            DB::commit();

            return response()->json([
                'message' => __('message.update_success'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
