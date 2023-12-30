<?php

namespace App\Imports;

use App\Rules\Admin\CheckDuplicateExcel;
use App\Rules\Admin\CheckNameMaster;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BaseMasterImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts, SkipsEmptyRows
{
    protected $model;
    protected $companyId;
    public $error = false;

    public function __construct($model)
    {
        $user = Auth::user();
        $this->companyId = $user->company_id;
        $this->model = $model;
    }

    public function collection(Collection $rows)
    {
        $validator = Validator::make($rows->toArray(), $this->rulesCheck($rows), $this->messagesCheck());
        $validator->validate();
        DB::beginTransaction();
        try {
            foreach ($rows as $row) {
                $name = $row['name'];
                $this->model::create(['company_id' => $this->companyId, 'name' => $name]);
            }
            DB::commit();
        } catch (\Throwable $th) {
            Log::error($th);
            DB::rollBack();
            return response()->json(['error' => __('message.server_error')], 403);
        }
        return true;
    }

    public function rulesCheck($rows)
    {
        return [
            '*.name' => ['required', 'max:255', new CheckDuplicateExcel($rows, 'name'), new CheckNameMaster($this->model)],
        ];
    }

    public function messagesCheck()
    {
        return [
            '*.name.required' => __('message.import.employee.last_name'),
            '*.name.max' => __('message.import.max.name'),
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
