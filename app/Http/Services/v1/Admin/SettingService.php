<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SettingService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Setting();
    }

    /**
     * @param false $id
     * @return Setting[]|\Illuminate\Database\Eloquent\Collection
     */
    public function getSetting($id = false)
    {
        if ($id != false) {
            return Setting::find($id);
        }

        return Setting::all();
    }

    public function getByCompanyId($companyId)
    {
        return $this->query->where('company_id', $companyId)->first();
    }

    /**
     * @param Request $request
     * @param string $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $message = '')
    {
        // $company_id = Auth::user()->company->id;
        $company_id = $request->company_id;

        $data = [
            'format_date' => $request->format_date,
            'time_zone' => $request->time_zone,
            'company_id' => $company_id,
            'locale' => $request->locale,
            // 'OT_after_office_hours'=>$request->OT_after_office_hours,
            // 'OT_weekend'=>$request->OT_weekend,
            // 'OT_holiday'=>$request->OT_holiday,
        ];

        try {
            DB::beginTransaction();

            $setting = $this->query->create($data);
            $setting->save();

            DB::commit();

            return response()->json([
                'message' => __('message.created_success'),
                'data' => $setting,
                'status' => Response::HTTP_OK,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->errorResponse();
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->only($this->model->getFillable());
        $user = Auth::user();

        $company_id = $user->company_id;
        $data['company_id'] = $company_id;

        $setting_id = $user->company->setting->id;
        $setting = $this->query->find($setting_id);

        try {
            DB::beginTransaction();

            if (empty($setting)) {
                return response()->json([
                    'message' => __('message.not_found'),
                ]);
            }

            $setting->update($data);
            $setting->save();

            DB::commit();

            return response()->json([
                'message' => __('message.update_success'),
                'status' => Response::HTTP_OK,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return $this->errorResponse();
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
        $setting = $this->query->find($id);

        if (empty($setting)) {
            return response()->json([
                'message' => __('message.not_found'),
            ]);
        }

        $setting->delete();

        return response()->json([
            'message' => __('message.delete_success'),
        ]);
    }

    public function updateByCompanyId($data, $companyId)
    {
        $this->query->where('company_id', $companyId)->update($data);
    }
}
