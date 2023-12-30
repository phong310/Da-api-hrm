<?php

namespace App\Exports;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BaseExport implements FromCollection, WithHeadings
{
    /** @var Model */
    protected $model;
    protected $headers;
    protected $rows = '';
    protected $companyId;

    public function __construct($model, array $headers, Request $request)
    {
        $user = Auth::user();
        $this->companyId = $user->company_id;
        $this->model = $model;
        $this->headers = array_diff($headers ?: ['id'], ['id']);
        if ($request->has('rows')) {
            $this->rows = $request->input('rows');
        }
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        if ($this->rows == 'all') {
            return $this->model->query()->where('company_id', $this->companyId)->get($this->headers);
        } else {
            $array = explode('-', $this->rows);
            $array = array_map('intval', $array);

            return $this->model->query()->where('company_id', $this->companyId)->whereIn('id', $array)->get($this->headers);
        }
    }

    public function headings(): array
    {
        return $this->headers;
    }
}
