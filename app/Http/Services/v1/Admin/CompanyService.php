<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Company;
use App\Models\Master\WorkingDay;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\ActiveCompanyNotification;
use App\Transformers\CompanyTransformer;
use Carbon\Carbon;
use Database\Seeders\InitDefaultCompanySeeder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class CompanyService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Company();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $kols = collect($collection)->transformWith(new CompanyTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $kols;
    }

    /**
     * @param false $id
     * @return Company[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getCompany($id = false)
    {
        if ($id != false) {
            return Company::find($id);
        }

        return Company::all();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $data = $request->only($this->model->getFillable());

        try {
            DB::beginTransaction();
            $company = $this->query->create($data);
            $company->logo = $this->uploadImage($company);
            $company->save();

            DB::commit();

            return response()->json([
                'message' => __('Thêm mới công ty thành công !'),
                'data' => $company,
                'status' => Response::HTTP_OK,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json([
                'message' => __('message.error_occurred'),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR,
            ]);
        }
    }

    /**
     * @param $company_id
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|null
     */
    public function getSettingOfCompany($company_id)
    {
        return Setting::query()->where(['company_id' => $company_id])->first();
    }

    public function storeNew(Request $request)
    {
        $data = $request->only($this->model->getFillable());
        $company = $this->query->create($data);
        $company->logo = $this->uploadImage($company, $request->logo);
        $company->save();
        return $company;
    }

    /**
     * @param $company
     * @return bool
     */
    public function uploadImage($company)
    {
        $filename = $company->logo;
        $folder_name = '/companies/' . $company->id . '/logos';
        if (request()->hasFile('logo')) {
            if ($filename !== null) {
                Storage::disk('public')->delete($filename);
            }

            // Storage::disk('public')->delete($filename);
            $logo = $this->request->file('logo');
            $filename = Storage::disk('public')->put(
                $folder_name,
                $logo
            );
        }

        return $filename;
    }

    public function updateInfo(Request $request, $company_id)
    {
        $data = $request->only($this->model->getFillable());
        $company = $this->query->find($company_id);

        if ($request->logo) {
            $logo = $this->uploadImage($company, $request->logo);
            $data['logo'] = $logo;
        }

        $company->update($data);
        return response()->json([
            'message' => __('message.update_success'),
            'data' => array_merge($data, ['id' => $company_id]),
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->only($this->model->getFillable());
        $company = $this->query->find($id);


        try {
            DB::beginTransaction();

            if (empty($company)) {
                return response()->json([
                    'message' => __('Không tìm thấy !'),
                ]);
            }
            $data['logo'] = $this->uploadImage($company);
            $company->update($data);

            if ($data['status'] == Company::STATUS['ACTIVE']) {
                $seeder = new InitDefaultCompanySeeder($id);
                $seeder->run();

                $user = User::where('company_id', $id)->first();
                $user->notify(new ActiveCompanyNotification());
            }

            DB::commit();

            return response()->json([
                'message' => __('Update thành công'),
                'data' => array_merge($data, ['id' => $id]),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return response()->json(['error' => 'server_error'], 500);
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @param false $isForceDelete
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id, $isForceDelete = false)
    {

        if ($isForceDelete) {
            $company = $this->query->onlyTrashed()->find($id);

            if (!$company) {
                return response()->json([
                    'message' => __('message.not_found'),
                ]);
            }

            $company->is_force_delete = $isForceDelete;

            $company->forceDelete();

            return response()->json([
                'message' => __('Xóa thành công'),
                'data' => $company,
            ]);
        } else {
            $company = $this->query->find($id);

            if (!$company) {
                return response()->json([
                    'message' => ('Không tìm thấy'),
                ]);
            }

            $company->is_force_delete = $isForceDelete;

            $company->delete();

            return response()->json([
                'message' => __('Xóa thành công'),
                'data' => $company,
            ]);
        }
    }

    public function createSettingByCompany($data)
    {
        return Setting::create($data);
    }

    public function createWorkingDayByCompany($data)
    {
        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);
        $startLunchBreak = Carbon::parse($data['start_lunch_break']);
        $endLunchBreak = Carbon::parse($data['end_lunch_break']);
        $totalTime = $startTime->floatDiffInMinutes($endTime);

        if ($data['start_lunch_break'] && $data['end_lunch_break']) {
            $totalTime -= $startLunchBreak->floatDiffInMinutes($endLunchBreak);
        }

        foreach ($data['name'] as $key => $value) {
            $newValue = $this->insertDataWorking($data, $key, $value);
            $newValue['total_working_time'] = $totalTime;
            WorkingDay::create($newValue);
        }
    }

    public function insertDataWorking($data, $key, $value)
    {
        $params = [];
        $params['name'] = $value;
        $params['day_in_week_id'] = $data['day_in_week_id'][$key];
        $params['type'] = $data['type'];
        $params['start_time'] = $data['start_time'];
        $params['end_time'] = $data['end_time'];
        $params['start_lunch_break'] = $data['start_lunch_break'];
        $params['end_lunch_break'] = $data['end_lunch_break'];
        $params['company_id'] = $data['company_id'];

        return $params;
    }
}
