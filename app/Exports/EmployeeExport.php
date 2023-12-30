<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Master\Branch;
use App\Models\Master\Country;
use App\Models\Master\Department;
use App\Models\Master\Position;
use App\Models\Master\Title;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;

class EmployeeExport implements FromCollection, WithHeadings, WithMapping, WithEvents
{
    public function collection()
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        return Employee::query()
            ->where('company_id', $companyId)
            ->with([
                'user',
                'information',
                'branch',
                'department',
                'position',
                'information.title',
                'information.country',
                'information.addresses',
                'information.identificationCards'
            ])->get();
    }

    public function headings(): array
    {
        return [
            // Thông tin chung
            'first_name',
            'last_name',
            'sex',
            'birthday',
            'nickname',
            'marital_status',
            'email',
            'phone',

            // Thông tin nhân viên
            'card_number',
            'employee_code',
            'branch',
            'department',
            'position',
            'title',
            'country',
            'ethnic',
            'date_start_work',
            'official_employee_date',
            'note',

            // Thông tin người dùng
            'information_email',
            'user_name',

            // Địa chỉ
            'province',
            'district',
            'ward',
            'address',

            // Giấy tờ tuỳ thân
            'ID_no',
            'issued_by',
            'issued_date',
            'ID_expire'
        ];
    }

    public function map($row): array
    {
        return [
            // Thông tin chung
            $row->information->first_name ?? null,
            $row->information->last_name ?? null,
            $row->information->sex == 1 ? "Nam" : "Nữ",
            $row->information->birthday ?? null,
            $row->information->nickname ?? null,
            $row->information->marital_status == 1 ? "Độc thân" : "Đã kết hôn",
            $row->information->email ?? null,
            $row->information->phone ?? null,

            // Thông tin nhân viên
            $row->card_number ?? null,
            $row->employee_code ?? null,
            $row->branch->name ?? null,
            $row->department->name ?? null,
            $row->position->name ?? null,
            $row->information->title->name ?? null,
            $row->information->country->name ?? null,
            $row->information->ethnic ?? null,
            $row->date_start_work ?? null,
            $row->official_employee_date ?? null,
            $row->information->note ?? null,

            // Thông tin người dùng
            $row->user->email ?? null,
            $row->user->user_name ?? null,

            // Địa chỉ
            $row->information->addresses->pluck('province')->implode(', ') ?? null,
            $row->information->addresses->pluck('district')->implode(', ') ?? null,
            $row->information->addresses->pluck('ward')->implode(', ') ?? null,
            $row->information->addresses->pluck('address')->implode(', ') ?? null,

            // Giấy tờ tuỳ thân
            $row->information->identificationCards->pluck('ID_no')->implode(', ') ?? null,
            $row->information->identificationCards->pluck('issued_by')->implode(', ') ?? null,
            $row->information->identificationCards->pluck('issued_date')->implode(', ') ?? null,
            $row->information->identificationCards->pluck('ID_expire')->implode(', ') ?? null,
        ];
    }

    public function registerEvents(): array
    {
        $sexList = ['Nam', 'Nữ'];
        $maritalStatusList = ['Độc thân', 'Đã kết hôn'];

        $user = Auth::user();
        $companyId = $user->company_id;

        $options = [
            'sex' => $sexList,
            'marital_status' => $maritalStatusList,
            'branch' => Branch::query()->where('company_id', $companyId)->pluck('name')->toArray(),
            'department' => Department::query()->where('company_id', $companyId)->pluck('name')->toArray(),
            'position' => Position::query()->where('company_id', $companyId)->pluck('name')->toArray(),
            'title' => Title::query()->where('company_id', $companyId)->pluck('name')->toArray(),
            'country' => Country::query()->pluck('name')->toArray(),
        ];

        $eventListener = function (AfterSheet $event) use ($options) {
            $startRow = 2;
            $lastRow = $event->sheet->getHighestRow();

            foreach ($options as $column => $list) {
                $columnIndex = array_search($column, $this->headings()) + 1;
                $columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($columnIndex);

                for ($row = $startRow; $row <= $lastRow + 50; $row++) {
                    $event->sheet->getCell("{$columnLetter}{$row}")
                        ->getDataValidation()->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_LIST)
                        ->setErrorStyle(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::STYLE_INFORMATION)
                        ->setAllowBlank(false)
                        ->setShowInputMessage(true)
                        ->setShowErrorMessage(true)
                        ->setShowDropDown(true)
                        ->setErrorTitle('Lỗi')
                        ->setError('Giá trị không hợp lệ')
                        ->setPromptTitle("Chọn $column")
                        ->setPrompt("Chọn một $column từ danh sách")
                        ->setFormula1('"' . implode(',', $list) . '"');
                }
            }
        };

        return [
            AfterSheet::class => $eventListener,
        ];
    }
}
