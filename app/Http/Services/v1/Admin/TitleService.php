<?php

namespace App\Http\Services\v1\Admin;

use App\Exports\BaseExport;
use App\Exports\BaseExportTemplate;
use App\Imports\BaseMasterImport;
use App\Models\Employee;
use App\Models\Master\Title;
use App\Repositories\Interfaces\TitleInterface;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TitleService extends BaseMasterService
{
    protected $title;

    protected $model;

    protected $query;

    protected $request;

    /**
     * @return mixed|void
     */
    public function setModel()
    {
        $this->model = new Title();
    }

    protected function setQuery()
    {
        $this->query = $this->model->query();
    }

    public function __construct(TitleInterface $title)
    {
        $this->title = $title;
        $this->request = request();
        $this->setModel();
        $this->setQuery();
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export(Request $request)
    {
        return Excel::download(new BaseExport(new Title(), ['id', 'name'], $request), 'titles.xlsx');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportTemplate()
    {
        return Excel::download(new BaseExportTemplate('Title'), 'titles.xlsx');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function import()
    {
        $import = new BaseMasterImport(Title::class);
        Excel::import($import, request()->file('file'));
        // Log::error($import->errors());
        return response()->json([
            'message' => 'Import success',
        ], 200);
    }

    public function destroy(Request $request, $id, $isForceDelete = false)
    {
        $companyId = $this->getCompanyId();
        $titleExists = Employee::query()->where('company_id', $companyId)->whereHas('personalInformation', function ($q) use ($id) {
            $q->where('title_id', $id);
        })->exists();

        if ($titleExists) {
            return response()->json([
                'message' => __('message.delete_title_faild'),
            ], 403);
        }

        $instance = $this->query->findOrFail($id);
        $instance->delete();
        return response()->json([
            'message' => __('message.delete_success'),
        ]);
    }

    public function updateMulti($titleName, $companyId)
    {
        $oldData = $this->title->getByCompanyId($companyId);

        foreach ($titleName as $item) {
            $newData = ['name' => $item, 'company_id' => $companyId];
            Title::query()->updateOrCreate($newData, $newData);
        }

        foreach ($oldData as $d) {
            if (!in_array($d['name'], $titleName)) {
                $d->forceDelete();
            }
        }
    }
}
