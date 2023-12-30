<?php

namespace App\Imports;

use App\Models\Address;
use App\Models\Employee;
use App\Models\IdentificationCard;
use App\Models\Master\Branch;
use App\Models\Master\Country;
use App\Models\Master\Department;
use App\Models\Master\Position;
use App\Models\Master\Title;
use App\Models\PersonalInformation;
use App\Models\Role;
use App\Models\User;
use App\Rules\Admin\CheckEmployeeExist;
use App\Rules\Admin\CheckInformationExist;
use App\Rules\Admin\CheckUserExist;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeeImport implements ToCollection, WithHeadingRow, WithChunkReading, WithBatchInserts, SkipsEmptyRows
{
    public function collection(Collection $rows)
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        $rules = [
            '*.first_name' => ['required', 'max:80'],
            '*.last_name' => ['required', 'max:80'],
            '*.employee_code' => ['required', 'max:80', new CheckEmployeeExist()],
            '*.birthday' => ['required', 'regex:/^\d{2}-\d{2}-\d{4}$/', 'before_or_equal:today'],
            '*.marital_status' => 'required',
            '*.sex' => 'required',
            '*.email' => ['required', 'email', 'max:80', new CheckInformationExist()],
            '*.phone_number' => ['required', 'regex:/^[0-9]{9,12}$/'],
            '*.country' => 'required',
            '*.title' => ['required', 'exists:m_titles,name,company_id,' . $companyId],
            '*.user_name' => ['required', 'max:80', new CheckUserExist()],
            '*.user_email' => ['required', 'email', 'max:80', new CheckUserExist()],
            '*.password' => 'required',
            '*.card_number' => ['nullable', 'max:80', new CheckEmployeeExist()],
            '*.date_start_work' => ['required', 'regex:/^\d{2}-\d{2}-\d{4}$/'],
            '*.official_employee_date' => ['nullable', 'regex:/^\d{2}-\d{2}-\d{4}$/', 'after_or_equal:*.date_start_work'],
            '*.position' => ['required', 'exists:m_positions,name,company_id,' . $companyId],
            '*.department' => ['required', 'exists:m_departments,name,company_id,' . $companyId],
            '*.branch' => ['required', 'exists:m_branches,name,company_id,' . $companyId],
            '*.role' => ['required', 'exists:roles,name,company_id,' . $companyId],
            '*.province' => ['nullable', 'max:80'],
            '*.district' => ['nullable', 'max:80'],
            '*.ward' => ['nullable', 'max:80'],
            '*.address' => ['nullable', 'max:100'],
            '*.id_no' => ['nullable', 'regex:/^[0-9]{9,12}$/'],
            '*.issued_by' => ['nullable', 'max:80'],
            '*.issued_date' => ['nullable', 'regex:/^\d{2}-\d{2}-\d{4}$/'],
            '*.id_expire' => ['nullable', 'regex:/^\d{2}-\d{2}-\d{4}$/', 'after:*.issued_date'],

        ];


        $messages = [
            '*.first_name.required' => __('message.import.employee.first_name'),
            '*.last_name.required' => __('message.import.employee.last_name'),
            '*.sex.required' => __('message.import.required.sex'),
            '*.birthday.required' => __('message.import.employee.birthday'),
            '*.birthday.regex' => __('message.import.employee.date_format'),
            '*.birthday.before_or_equal' => __('message.import.employee.birthday_before_or_equal'),
            '*.marital_status.required' => __('message.import.required.martial_status'),
            '*.email.required' => __('message.import.employee.email'),
            '*.email.email' => __('message.import.employee.email_format'),
            '*.phone_number.required' => __('message.import.employee.phone_number'),
            '*.phone_number.regex' => __('message.import.employee.phone_format'),
            '*.employee_code.required' => __('message.import.employee.employee_code'),
            '*.employee_code.max' => __('message.import.employee.max_value_80'),
            '*.branch.required' => __('message.import.employee.branch'),
            '*.branch.exists' => __('message.import.employee.exists'),
            '*.department.required' => __('message.import.employee.department'),
            '*.department.exists' => __('message.import.employee.exists'),
            '*.position.required' => __('message.import.employee.position'),
            '*.position.exists' => __('message.import.employee.exists'),
            '*.title.required' => __('message.import.employee.title'),
            '*.title.exists' => __('message.import.employee.exists'),
            '*.country.required' => __('message.import.employee.country'),
            '*.date_start_work.required' => __('message.import.employee.date_start_work'),
            '*.date_start_work.regex' => __('message.import.employee.date_format'),
            '*.official_employee_date.regex' => __('message.import.employee.date_format'),
            '*.official_employee_date.after_or_equal' => __('message.import.employee.official_date_after_employee_date'),
            '*.user_email.required' => __('message.import.employee.user_email'),
            '*.user_name.required' => __('message.import.employee.user_name'),
            '*.password.required' => __('message.import.employee.password'),
            '*.status.required' => __('message.import.employee.status'),
            '*.role.required' => __('message.import.employee.role'),
            '*.issued_date.regex' =>  __('message.import.employee.date_format'),
            '*.issued_date.required' =>  __('message.import.required.issued_date'),
            '*.id_expire.regex' => __('message.import.employee.date_format'),
            '*.id_expire.after' => __('message.import.employee.id_expire_after_issued_date'),
            '*.id_expire.required' => __('message.import.required.id_expire'),
            '*.card_number.numeric' => __('message.import.employee.is_number'),
            '*.user_email.email' =>  __('message.import.employee.email_format'),
            '*.role.exists' => __('message.import.employee.exists'),
            '*.first_name.max' => __('message.import.employee.max_value_80'),
            '*.last_name.max' => __('message.import.employee.max_value_80'),
            '*.nickname.max' => __('message.import.employee.max_value_80'),
            '*.email.max' => __('message.import.employee.max_value_80'),
            '*.user_email.max' => __('message.import.employee.max_value_80'),
            '*.user_name.max' => __('message.import.employee.max_value_80'),
            '*.card_number.max' => __('message.import.employee.max_value_80'),
            '*.province.max' => __('message.import.employee.max_value_80'),
            '*.province.required' => __('message.import.required.province'),
            '*.district.max' => __('message.import.employee.max_value_80'),
            '*.district.required' => __('message.import.required.district'),
            '*.ward.max' => __('message.import.employee.max_value_80'),
            '*.ward.required' => __('message.import.required.ward'),
            '*.address.max' => __('message.import.employee.max_value_100'),
            '*.address.required' => __('message.import.required.address'),
            '*.id_no.max' => __('message.import.employee.max_value_80'),
            '*.id_no.regex' =>  __('message.import.employee.id_no_format'),
            '*.id_no.required' => __('message.import.required.id_no'),
            '*.id_no.unique' => __('message.data_exits'),
            '*.issued_by.max' => __('message.import.employee.max_value_80'),
            '*.issued_by.required' => __('message.import.required.issued_by'),
        ];

        foreach ($rows as $index => $row) {
            $hasAddressField = isset($row['province']) || isset($row['district']) || isset($row['ward']) || isset($row['address']);
            $hasInfoField = isset($row['id_no']) || isset($row['id_expire']) || isset($row['issued_by']) || isset($row['issued_date']);
            if ($hasAddressField) {
                $rules["{$index}.province"] = ['required', 'max:80'];
                $rules["{$index}.district"] = ['required', 'max:80'];
                $rules["{$index}.ward"] = ['required', 'max:80'];
                $rules["{$index}.address"] = ['required', 'max:80'];
            }
            if ($hasInfoField) {
                $rules["{$index}.issued_date"] = ['required', 'regex:/^\d{2}-\d{2}-\d{4}$/'];
                $rules["{$index}.id_no"] = ['required', 'regex:/^[0-9]{9,12}$/', 'unique:identification_cards'];
                $rules["{$index}.issued_by"] = ['required', 'max:80'];
                $rules["{$index}.id_expire"] = ['required', 'regex:/^\d{2}-\d{2}-\d{4}$/', 'after:*.issued_date'];
            }
        }


        $validator = Validator::make($rows->toArray(), $rules, $messages);
        $validator->validate();
        $departments = Department::where('company_id', $companyId)->pluck('id', 'name')->toArray();
        $countries = Country::pluck('id', 'name')->toArray();
        $positions = Position::where('company_id', $companyId)->pluck('id', 'name')->toArray();
        $branches = Branch::where('company_id', $companyId)->pluck('id', 'name')->toArray();
        $titles = Title::where('company_id', $companyId)->pluck('id', 'name')->toArray();
        $roles = Role::where('company_id', $companyId)->pluck('id', 'name')->toArray();
        DB::beginTransaction();

        try {
            foreach ($rows as $row) {
                $departmentId = $departments[$row['department']];
                $countryId = $countries[$row['country']];
                $positionId = $positions[$row['position']];
                $branchId = $branches[$row['branch']];
                $titleId = $titles[$row['title']];
                $roleId = $roles[$row['role']];

                $information = PersonalInformation::create([
                    'first_name' => $row['first_name'],
                    'last_name' => $row['last_name'],
                    'nickname' => $row['nickname'],
                    'birthday' => $this->formatTime($row['birthday']),
                    'marital_status' => $row['marital_status'] == __('message.import.employee.martial_status.single') ? Employee::MARTIAL_STATUS['single'] : Employee::MARTIAL_STATUS['married'],
                    'sex' => $row['sex'] == __('messsage.import.employee.sex.male') ? Employee::SEX['male'] : Employee::SEX['female'],
                    'email' => $row['email'],
                    'phone' => $row['phone_number'],
                    'note' => $row['note'],
                    'country_id' => $countryId,
                    'ethnic' => $row['ethnic'],
                    'title_id' => $titleId,
                ]);

                $employee = Employee::create([
                    'employee_code' => $row['employee_code'],
                    'card_number' => $row['card_number'],
                    'official_employee_date' => $this->formatTime($row['official_employee_date']),
                    'date_start_work' => $this->formatTime($row['date_start_work']),
                    'position_id' => $positionId,
                    'department_id' => $departmentId,
                    'branch_id' => $branchId,
                    'personal_information_id' => $information->id,
                    'status' => $row['status'] ?? 1,
                    'company_id' => $companyId
                ]);

                Address::create([
                    'province' => $row['province'] ?? '',
                    'district' => $row['district'] ?? '',
                    'ward' => $row['ward'] ?? '',
                    'address' => $row['address'] ?? '',
                    'personal_information_id' => $information->id,
                    'type' => 0,
                ]);

                if (isset($row['id_expire']))
                    IdentificationCard::create([
                        'ID_no' => $row['id_no'],
                        'issued_date' => $this->formatTime($row['issued_date']),
                        'issued_by' => $row['issued_by'],
                        'ID_expire' => $this->formatTime($row['id_expire']),
                        'personal_information_id' => $information->id,
                        'type' => 0,
                    ]);
                $user = User::create([
                    'user_name' => $row['user_name'],
                    'email' => $row['user_email'],
                    'password' => bcrypt($row['password']),
                    'employee_id' => $employee->id,
                    'company_id' => $companyId
                ]);

                $user->assignRole($roleId);
            }
            DB::commit();
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();
            return response()->json(['error' => __('message.import.not_found')], 403);
        }
    }

    function formatTime($value)
    {
        if ($value) {
            return (new Carbon(strtotime($value)))->toDateString();
        }
    }

    public function batchSize(): int
    {
        return 200;
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
