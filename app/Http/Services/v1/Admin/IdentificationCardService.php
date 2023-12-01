<?php

namespace App\Http\Services\v1\Admin;

use App\Http\Services\v1\BaseService;
use App\Models\Employee;
use App\Models\IdentificationCard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IdentificationCardService extends BaseService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new IdentificationCard();
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->addDefaultFilter();

        return response()->json($data);
    }

    /**
     * @param Request $request
     * @param $id
     * @param false $isForceDelete
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id, $isForceDelete = false)
    {
        $instance = $this->query->findOrFail($id);
        $instance->delete();

        return response()->json(['message' => __('message.delete_success')]);
    }

    /**
     * @param $employee_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getByEmployee($employee_id)
    {
        $idenCard = $this->query->whereHas('personalInformation', function ($q) use ($employee_id) {
            $q->whereHas('employee', function ($q) use ($employee_id) {
                $q->where(['employees.id' => $employee_id]);
            });
        })->get();

        return response()->json($idenCard);
    }

    /**
     * @param Request $request
     * @param $employeeId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateByEmployee(Request $request, $employeeId)
    {
        $data = $request->all();
        $employee = Employee::query()->where(['id' => $employeeId])->first();
        $idenCardsData = [$data['CMT'], $data['TCC']];
        DB::beginTransaction();

        try {
            if (!$idenCardsData || !$employee) {
                return response()->json([
                    'message' => __('message.not_found'),
                ], 404);
            }

            foreach ($idenCardsData as $idenCardData) {
                if (isset($idenCardData['id'])) {
                    if (
                        !$idenCardData['ID_expire'] &&
                        !$idenCardData['ID_no'] &&
                        !$idenCardData['issued_by'] &&
                        !$idenCardData['issued_date']
                    ) {
                        $instance = $this->query->findOrFail($idenCardData['id']);
                        $instance->delete();
                    } else {
                        $isUpdate = false;
                        $dataUpdate = [];
                        $idenData = IdentificationCard::find($idenCardData['id']);

                        if ($idenCardData['type'] == IdentificationCard::$Type['CMT']) {
                            $isUpdate = true;
                            $dataUpdate = $data['CMT'];
                        } elseif ($idenCardData['type'] == IdentificationCard::$Type['TCC']) {
                            $isUpdate = true;
                            $dataUpdate = $data['TCC'];
                        }

                        if ($idenData && $isUpdate) {
                            $idenData->update([
                                'ID_expire' => $dataUpdate['ID_expire'],
                                'ID_no' => $dataUpdate['ID_no'],
                                'issued_by' => $dataUpdate['issued_by'],
                                'issued_date' => $dataUpdate['issued_date'],
                            ]);
                        }
                    }
                } elseif (
                    $idenCardData['ID_expire'] &&
                    $idenCardData['ID_no'] &&
                    $idenCardData['issued_by'] &&
                    $idenCardData['issued_date']
                ) {
                    IdentificationCard::create([
                        'ID_expire' => $idenCardData['ID_expire'],
                        'ID_no' => $idenCardData['ID_no'],
                        'type' => $idenCardData['type'],
                        'personal_information_id' => $employee->personal_information_id,
                        'issued_by' => $idenCardData['issued_by'],
                        'issued_date' => $idenCardData['issued_date'],
                    ]);
                }
            }

            DB::commit();
            return response()->json([
                'message' => __('message.update_success'),
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(['error' => 'server_error'], 500);
        }
    }
}
