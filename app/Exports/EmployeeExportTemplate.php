<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Master\Country;
use App\Repositories\Interfaces\BranchInterface;
use App\Repositories\Interfaces\DepartmentInterface;
use App\Repositories\Interfaces\PositionInterface;
use App\Repositories\Interfaces\RoleInterface;
use App\Repositories\Interfaces\TitleInterface;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;

class EmployeeExportTemplate implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected  $users;
    protected  $selects;
    protected  $row_count;
    protected  $column_count;
    protected  $title;
    protected  $positon;
    protected  $branch;
    protected  $department;
    protected  $role;

    public function __construct(TitleInterface $title, PositionInterface $positon, BranchInterface $branch, DepartmentInterface $department, RoleInterface $role)
    {
        $user = Auth::user();
        $this->title = $title;
        $this->positon = $positon;
        $this->branch = $branch;
        $this->department = $department;
        $this->role = $role;
        $this->row_count = Employee::TEMPLATE_EMPLOYEE['ROW_COUNT'];
        $this->column_count = Employee::TEMPLATE_EMPLOYEE['COLUMN_COUNT'];
        $selects = $this->getDataForColumns($user);
        $this->selects = $selects;
    }

    public function collection()
    {
        return collect([]);
    }


    public function headings(): array
    {
        return [
            'first_name',
            'last_name',
            'sex',
            'marital_status',
            'birthday',
            'nickname',
            'email',
            'card_number',
            'phone_number',
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
            'user_email',
            'user_name',
            'role',
            'password',
            'province',
            'district',
            'ward',
            'address',
            'id_no',
            'issued_by',
            'issued_date',
            'id_expire'
        ];
    }


    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $row_count = $this->row_count;
                $column_count = $this->column_count;
                $cellRange = 'A1:AE60';
                $size = 14;

                $this->styleCol($event, $cellRange, $size);
                $defaults = $this->defaultCol();

                $this->getComments($event);
                for ($i = 2; $i <= $this->row_count; $i++) {
                    $event->sheet->getStyle("H{$i}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $event->sheet->getStyle("I{$i}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $event->sheet->getStyle("J{$i}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $event->sheet->getStyle("E{$i}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $event->sheet->getStyle("Q{$i}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $event->sheet->getStyle("R{$i}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $event->sheet->getStyle("AD{$i}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $event->sheet->getStyle("AB{$i}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                    $event->sheet->getStyle("AE{$i}")->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);
                }

                $titleColumns = $this->headings();
                foreach ($titleColumns as $titleColumn) {
                    $titleColumnIndex = array_search($titleColumn, $this->headings());
                    $titleColumnLetter = Coordinate::stringFromColumnIndex($titleColumnIndex + 1);
                    $event->sheet->getStyle("{$titleColumnLetter}1")->getFont()->setBold(true);
                }
                foreach ($defaults as $column => $value) {
                    $columnIndex = array_search($column, $this->headings());
                    $event->sheet->setCellValueByColumnAndRow($columnIndex + 1, 2, $value);
                }
                foreach ($this->selects as $select) {
                    $drop_column = $select['columns_name'];
                    $options = $select['options'];
                    // set dropdown list for first data row
                    $validation = $event->sheet->getCell("{$drop_column}2")->getDataValidation();
                    $validation->setType(DataValidation::TYPE_LIST);
                    $validation->setErrorStyle(DataValidation::STYLE_INFORMATION);
                    $validation->setAllowBlank(false);
                    $validation->setShowInputMessage(true);
                    $validation->setShowErrorMessage(true);
                    $validation->setShowDropDown(true);
                    $validation->setError('Value is not in list.');
                    $validation->setPromptTitle('Pick from list');
                    $validation->setFormula1(sprintf('"%s"', implode(',', $options)));

                    $default_value = reset($options);
                    $event->sheet->getCell("{$drop_column}2")->setValue($default_value);

                    for ($i = 3; $i <= $row_count; $i++) {
                        $event->sheet->getCell("{$drop_column}{$i}")->setDataValidation(clone $validation);
                    }
                }
                for ($i = 1; $i <= $column_count; $i++) {
                    $column = Coordinate::stringFromColumnIndex($i);
                    $event->sheet->getColumnDimension($column)->setAutoSize(false);
                    $event->sheet->getDelegate()->getRowDimension($i)->setRowHeight(35);
                    $event->sheet->getDelegate()->getColumnDimension($column)->setWidth(40);
                }
            },
        ];
    }


    public function styleCol($event, $cellRange, $size)
    {
        $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize($size);
    }

    public function defaultCol()
    {

        $defaults = [
            'first_name' => 'Dow',
            'last_name' => 'John',
            'birthday' => '01-01-2000',
            'nickname' => 'Doe',
            'email' => 'email@example.com',
            'card_number' => '0000',
            'phone_number' => '0123456789',
            'employee_code' => '1111',
            'ethnic' => 'Kinh',
            'date_start_work' => '01-01-2023',
            'official_employee_date' => '02-02-2023',
            'note' => 'Note',
            'user_email' => 'email@example.com',
            'user_name' => 'User Name',
            'password' => 'admin@123',
            'province' => 'Hà Nội',
            'district' => 'Hà Nội',
            'ward' => 'Văn Lý',
            'address' => '01 Văn Lý Hà Nam',
            'id_no' => '00100213302',
            'issued_by' => 'Hà Nội',
            'issued_date' => '20-10-2020',
            'id_expire' => '20-10-2024'
        ];
        return $defaults;
    }

    public function getDataForColumns($user)
    {
        $company_id = $user->company_id;
        $titles = $this->title->getArrayByCompany($company_id);
        $positions = $this->positon->getArrayByCompany($company_id);
        $branchs = $this->branch->getArrayByCompany($company_id);
        $departments = $this->department->getArrayByCompany($company_id);
        $roles = $this->role->getArrayByCompany($company_id);
        $countries = Country::all()->pluck('name')->toArray();
        $sex = [
            __('message.import.employee.sex.male'),
            __('message.import.employee.sex.female')
        ];
        $martial_status = [
            __('message.import.employee.martial_status.single'),
            __('message.import.employee.martial_status.married'),
        ];
        $selects = [
            ['columns_name' => 'C', 'options' => $sex],
            ['columns_name' => 'D', 'options' => $martial_status],
            ['columns_name' => 'K', 'options' => $branchs],
            ['columns_name' => 'L', 'options' => $departments],
            ['columns_name' => 'M', 'options' => $positions],
            ['columns_name' => 'N', 'options' => $titles],
            ['columns_name' => 'O', 'options' => $countries],
            ['columns_name' => 'V', 'options' => $roles],
        ];

        return $selects;
    }


    public function getComments($event)
    {
        $comments = [
            'A1' => 'First name is required',
            'B1' => 'Last name is required',
            'C1' => 'Sex is required',
            'D1' => 'Martial status is required',
            'E1' => 'Birth day is required',
            'G1' => 'Email is required',
            'J1' => 'Employee code is required',
            'K1' => 'Branch is required',
            'L1' => 'Department is required',
            'M1' => 'Position is required',
            'N1' => 'Title is required',
            'O1' => 'Country is required',
            'Q1' => 'Date start work is required',
            'T1' => 'User email is required',
            'V1' => 'Role is required',
            'W1' => 'Password is required',
        ];

        foreach ($comments as $cell => $comment) {
            $event->sheet->getDelegate()->getComment($cell)
                ->getText()
                ->createTextRun($comment)
                ->getFont()
                ->setSize(12);
        }
    }
}
