<?php

namespace App\Http\Services\v1\Admin;

use App\Exports\BaseExport;
use App\Exports\KindOfLeaveExportTemplate;
use App\Http\Services\v1\BaseService;
use App\Imports\KindOfLeaveImport;
use App\Models\Master\KindOfLeave;
use App\Repositories\Interfaces\Forms\CompensatoryLeaveInterface;
use App\Repositories\Interfaces\Forms\LeaveFormInterface;
use App\Repositories\Interfaces\KindOfLeaveInterface;
use App\Transformers\KindOfLeaveTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use Maatwebsite\Excel\Facades\Excel;

class KindOfLeaveService extends BaseService
{
    protected $kindOfLeave;
    protected $leaveForm;
    protected $compensatoryLeave;

    /**
     * Instantiate a new controller instance.
     *
     * @param KindOfLeaveInterface $kindOfLeave
     */
    public function __construct(
        KindOfLeaveInterface       $kindOfLeave,
        LeaveFormInterface         $leaveForm,
        CompensatoryLeaveInterface $compensatoryLeave
    ) {
        $this->kindOfLeave = $kindOfLeave;
        $this->leaveForm = $leaveForm;
        $this->compensatoryLeave = $compensatoryLeave;
        parent::__construct();
    }

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new KindOfLeave();
    }

    /**
     * @param $data
     * @return mixed
     */
    public function setTransformers($data)
    {
        $collection = $data->getCollection();

        return collect($collection)->transformWith(new KindOfLeaveTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $data = $request->only($this->model->getFillable());
        $kindOfLeave = $this->kindOfLeave->show($id);

        try {
            if (!$kindOfLeave) {
                return response()->json([
                    'message' => __('message.not_found'),
                ], 404);
            }
            $kindOfLeave->fill($data);
            $kindOfLeave->save();

            return response()->json([
                'message' => __('message.update_success'),
                'data' => $kindOfLeave,
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            return $this->errorResponse();
        }
    }

    // /**
    //  * @param Request $request
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function export(Request $request)
    // {
    //     return Excel::download(new BaseExport(new KindOfLeave(), ['id', 'name', 'symbol', 'type'], $request), 'kind_of_leave.xlsx');
    // }

    // /**
    //  * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
    //  */
    // public function exportTemplate()
    // {
    //     return Excel::download(new KindOfLeaveExportTemplate(), 'kind_of_leaves.xlsx');
    // }

    /**
     * @param Request $request
     * @param $id
     * @param false $isForceDelete
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id, $isForceDelete = false)
    {
        $companyId = $this->getCompanyId();
        $leaveFormExit = $this->leaveForm->getLeaveFormExit($id, $companyId);
        $CompensatoryLeaveExit = $this->compensatoryLeave->getCompensatoryLeave($id, $companyId);

        if ($leaveFormExit || $CompensatoryLeaveExit) {
            return response()->json([
                'message' => __('message.delete_kind_of_leave'),
            ], 403);
        }

        $instance = $this->query->findOrFail($id);
        $instance->delete();

        return response()->json([
            'message' => __('message.delete_success'),
        ]);
    }

    // /**
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function import()
    // {
    //     Excel::import(new KindOfLeaveImport(), request()->file('file'));

    //     return response()->json([
    //         'message' => 'Import success',
    //     ], 200);
    // }
}
