<?php

namespace App\Http\Services\v1\Admin;

use App\Exports\BaseExport;
use App\Exports\BaseExportTemplate;
use App\Imports\BaseMasterImport;
use App\Models\Master\Branch;
use App\Transformers\BranchTransformer;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Maatwebsite\Excel\Facades\Excel;

class BranchService extends BaseMasterService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Branch();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $diws = collect($collection)->transformWith(new BranchTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $diws;
    }

    public function getByCompanyId($companyId)
    {
        return $this->model->query()->where('company_id', $companyId)->get();
    }

    /**
     * @param $branch_name
     * @param $company_id
     */
    public function storeMulti($branch_name, $company_id)
    {
        $data = [];
        foreach ($branch_name as $item) {
            $data[] = ['name' => $item, 'company_id' => $company_id];
        }

        if (count($data)) {
            Branch::query()->insert($data);
        }
    }

    public function updateMulti($branchName, $companyId)
    {
        $oldData = $this->getByCompanyId($companyId);

        foreach ($branchName as $item) {
            $newData = ['name' => $item, 'company_id' => $companyId];
            $this->model->query()->updateOrCreate($newData, $newData);
        }

        foreach ($oldData as $d) {
            if (!in_array($d['name'], $branchName)) {
                $d->forceDelete();
            }
        }
    }

    // /**
    //  * @param Request $request
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function export(Request $request)
    // {
    //     return Excel::download(new BaseExport(new Branch(), ['id', 'name'], $request), 'branches.xlsx');
    // }

    // /**
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function exportTemplate()
    // {
    //     return Excel::download(new BaseExportTemplate('Branch'), 'branches.xlsx');
    // }

    // /**
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function import()
    // {
    //     $import = new BaseMasterImport(Branch::class);
    //     Excel::import($import, request()->file('file'));
    //     // Log::error($import->errors());
    //     return response()->json([
    //         'message' => 'Import success',
    //     ], 200);
    // }
}
