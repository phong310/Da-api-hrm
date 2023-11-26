<?php

namespace App\Http\Services\v1\Admin;

use App\Exports\BaseExport;
use App\Exports\BaseExportTemplate;
use App\Imports\BaseMasterImport;
use App\Models\Employee;
use App\Models\Master\Position;
use App\Repositories\Interfaces\PositionInterface;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PositionService extends BaseMasterService
{
    protected $position;

    protected $model;

    protected $query;

    protected $request;

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Position();
    }

    protected function setQuery()
    {
        $this->query = $this->model->query();
    }

    public function __construct(PositionInterface $position)
    {
        $this->position = $position;
        $this->request = request();
        $this->setModel();
        $this->setQuery();
    }

    // /**
    //  * @param Request $request
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function export(Request $request)
    // {
    //     return Excel::download(new BaseExport(new Position(), ['id', 'name'], $request), 'positions.xlsx');
    // }

    // /**
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function exportTemplate()
    // {
    //     return Excel::download(new BaseExportTemplate('Position'), 'positions.xlsx');
    // }

    // /**
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function import()
    // {
    //     $import = new BaseMasterImport(Position::class);
    //     Excel::import($import, request()->file('file'));
    //     // Log::error($import->errors());
    //     return response()->json([
    //         'message' => 'Import success',
    //     ], 200);
    // }


    public function destroy(Request $request, $id, $isForceDelete = false)
    {
        $companyId = $this->getCompanyId();
        $employeesPositionExists = Employee::query()->where('company_id', $companyId)->where('position_id', $id)->exists();
        if ($employeesPositionExists) {
            return response()->json([
                'message' => __('message.delete_position_faild'),
            ], 403);
        }

        $instance = $this->query->findOrFail($id);
        $instance->delete();
        return response()->json([
            'message' => __('message.delete_success'),
        ]);
    }


    public function updateMulti($positionName, $companyId)
    {
        $oldData = $this->position->getByCompanyId($companyId);

        foreach ($positionName as $item) {
            $newData = ['name' => $item, 'company_id' => $companyId];
            Position::query()->updateOrCreate($newData, $newData);
        }

        foreach ($oldData as $d) {
            if (!in_array($d['name'], $positionName)) {
                $d->forceDelete();
            }
        }
    }
}
