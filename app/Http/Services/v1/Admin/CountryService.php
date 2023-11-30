<?php

namespace App\Http\Services\v1\Admin;

use App\Exports\BaseExport;
use App\Exports\BaseExportTemplate;
use App\Imports\BaseMasterImport;
use App\Models\Master\Country;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CountryService extends BaseMasterService
{
    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Country();
    }

    // /**
    //  * @param Request $request
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function export(Request $request)
    // {
    //     return Excel::download(new BaseExport(new Country(), ['id', 'name'], $request), 'countries.xlsx');
    // }

    // /**
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function exportTemplate()
    // {
    //     return Excel::download(new BaseExportTemplate('Country name'), 'countries.xlsx');
    // }

    // /**
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function import()
    // {
    //     $import = new BaseMasterImport(Country::class);
    //     Excel::import($import, request()->file('file'));
    //     // Log::error($import->errors());
    //     return response()->json([
    //         'message' => 'Import success',
    //     ], 200);
    // }
}
