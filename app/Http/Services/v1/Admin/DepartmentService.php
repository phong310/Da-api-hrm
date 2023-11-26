<?php

namespace App\Http\Services\v1\Admin;

use App\Exports\BaseExport;
use App\Exports\BaseExportTemplate;
use App\Imports\BaseMasterImport;
use App\Models\Master\Department;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DepartmentService extends BaseMasterService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Department();
    }

    // /**
    //  * @param Request $request
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function export(Request $request)
    // {
    //     return Excel::download(new BaseExport(new Department(), ['id', 'name'], $request), 'departments.xlsx');
    // }

    // /**
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function exportTemplate()
    // {
    //     return Excel::download(new BaseExportTemplate('Department'), 'departments.xlsx');
    // }

    /**
     * @param $names
     * @param $company_id
     */
    public function storeMulti($names, $company_id)
    {
        $data = [];
        foreach ($names as $item) {
            $data[] = ['name' => $item, 'company_id' => $company_id];
        }

        if (count($data)) {
            Department::query()->insert($data);
        }
    }

    public function updateMulti($departmentName, $companyId)
    {
        $oldData = $this->getByCompanyId($companyId);

        foreach ($departmentName as $item) {
            $newData = ['name' => $item, 'company_id' => $companyId];
            $this->model->query()->updateOrCreate($newData, $newData);
        }

        foreach ($oldData as $d) {
            if (!in_array($d['name'], $departmentName)) {
                $d->forceDelete();
            }
        }
    }

    // /**
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function import()
    // {
    //     $import = new BaseMasterImport(Department::class);
    //     Excel::import($import, request()->file('file'));
    //     // Log::error($import->errors());
    //     return response()->json([
    //         'message' => 'Import success',
    //     ], 200);
    // }

    public function getByCompanyId($companyId)
    {
        return $this->model->query()->where('company_id', $companyId)->get();
    }
}
