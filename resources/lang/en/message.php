<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
    */
    'not_found' => 'Not found',
    'timekeeping_success' => 'Timekeeping successfully',
    'created_success' => 'Created successfully',
    'sync_success' => 'Data is syncing, please wait',
    'approved_successfully' => 'Approved successfully',
    'invalid_data' => 'Invalid data',
    'checked_successfully' => 'Checked successfully',
    'request_approved_successfully' => 'Submitted for approval successfully',
    'update_success' => 'Updated successfully',
    'delete_success' => 'Deleted successfully',
    'not_delete' => 'Not delete',
    'not_delete_salay_in_await' => 'Salary cannot be deleted in pending approval status',
    'not_delete_salary_in_confirmed' => 'Salary cannot be deleted in approval status',
    'not_delete_salary_in_public' => 'Salary cannot be deleted in public status',
    'restore_success' => 'Restored successfully',
    'working_day_exits' => 'Working day already exists',
    'date_exits' => 'Holiday already exists',
    'data_exits' => 'Data already exists',
    'server_error' => 'Maintenance system!',
    'not_active' => 'Your company is waiting for approval, please try again later',
    'employee_quits' => 'Employee account has quit!',
    'kind_leave_id' => 'kind leave',
    'reason' => 'reason',
    "basic_salary" =>  'Basic salary',
    "hourly_wages" => 'Hourly',
    "taxable_income" => 'Income taxes',
    "personal_income_tax" => 'Personal income tax',
    "syndicate_money" => 'Qũy công đoàn',
    'start_time' => 'start time',
    'end_time' => 'end time',
    'approver_id' => 'approver',
    'approver_id_1' => 'approver 1',
    'approver_id_2' => 'approver 2',
    'note' => 'note',
    'current_password' => 'The password is incorrect',
    'exists_time_application' => 'Day :value, already exists this time. Please choose another time period',
    'run_out_of_application' => 'Day :attribute :value, :application_type application already exist. Please choose other day',
    'not_in_working_day_date' => 'Day :value does not belong to working time',
    'not_in_working_day' => 'The :attribute is not in working day',
    'is_form_processed' => 'This application form is processed',
    'not_permission' => 'Not permission',
    'not_all_approved_salaries' => 'You need to review or finalize all salaries in the payroll',
    'not_all_approved_checked' => 'You need to check all employees before submitting the approval request',
    'email_not_found' => 'Your email does not exist. Please register your email',
    'not_overtime_in_working_day' => 'OT time coincides with working time',
    'not_create_leave_form' => 'Your number of leave days is not enough, please switch to Unpaid leave or check your applications pending approval.',
    'email_reset_pass' => [
        'subject' => 'Reset Password HRM!',
        'greeting' => 'Reset Password HRM!',
        'content' => 'You are receiving this email because we received a password reset request for your account.',
        'content_if' => 'If you did not request a password reset, no further action is required.',
        'reset_password' => 'Reset Password',
    ],
    'create_company' => [
        'subject' => 'Create new company!',
        'greeting' => 'Create new company!',
        'content' => 'You have successfully registered your company. Please wait for the admin to approve your company.',
    ],
    'active_company' => [
        'subject' => 'Approved company!',
        'greeting' => 'Approved company!',
        'content' => 'Your company has been approved successfully. Please click the button below to create employee information for your company.',
        'create_employee' => 'Create new employee'
    ],
    'send_mail_success' => 'We have e-mailed your password reset link!',
    'token_invalid' => 'This password reset token is invalid',
    'reset_pass_success'  => 'Reset password successfully!',
    'overtime_overlaps' => 'Overtime has been overlapped',
    'overtime_exist' => 'Time does not belong to OT time',
    'validate' => [
        'working_day' => [
            'end_time_after_start_time' => 'The end time must be after the start time',
            'end_lunch_break_after_start_lunch_break' => 'The lunch break end time must be after the lunch break start time',
        ],
        'required' => [
            'day_in_week_id' => 'The weekday must be filled',
            'time_zone' => 'The timezone must be filled',
            'format_date' => 'The format date must be filled',
            'locale' => 'The locale must be filled',
            "company" => [
                "name" => "The company name field must be filled in",
                "address" => "The company address field must be filled in",
                "phone_number" => "The company phone number field must be filled in",
                "tax_code" => "The tax code field must be filled out",
                "representative" => "The representative field must be filled out",
                "type_of_business" => "The business type field must be filled out"
            ]
        ],
        'unique' => [
            "company" => [
                "name" => "The company name already exists",
                "tax_code" => "Tax code already exists",
                "phone_number" => "Phone number already exists",
            ]
        ]
    ],
    'labor_contract_exits' => 'The labor contract already exists',
    'salary_sheets_date_exists' => "This time period has existed. Please choose another time period",
    "labor_expire_date" => 'The expiration date field must be greater than the effective date !',
    "labor_current_date" => "The expiration date field must be greater than the current date !",
    "personal_in_come_tax_exits" => "The value entered must be greater than the previous value !",
    'percent_personal_in_come_tax_exits' => 'The tax rate field must have a value greater than before !',
    "salary_closing" => "Current salary cannot be changed",
    "other_amount_allowance_exits" => "Other amount allowance exits !",
    "unknown" => "Unknown",
    "time_lunch_break_exist" => "Break time must be between the end time and the start time !",
    "validate_to_date" => "The end date must be greater than the start date",
    "validate_school_name" => "School names must not exceed 50 characters",
    "percent_valid" => "This field must be in the range 0 - 1000",
    "import_fails" => "Upload failed due to duplicate records",
    "import_success" => "Upload successfully",
    "personal_income_tax" => [
        "min_value" => "The minimum value of personal income tax cannot be left blank",
        "percent" => "Tax rate percentage cannot be left blank"
    ],
    "school_name_empty" => "The field name school must be entered",
    "validate_descriptions" => "The field description must be entered",
    "delete_labor_contract_type_exist" => "Unable to delete the type of contract in use !",
    "end_time_after_start_time" => "The end time must be greater than the start time",
    "delete_allowance_exist" => "Unable to delete allowances that are being used !",
    "leave_form_holiday_exist" => "The time off is during the holiday period !",
    "delete_position_faild" => "Position cannot be deleted. Because the position has been installed for employees",
    "delete_department_faild" => "Department cannot be deleted. Because department has been installed for company",
    "delete_title_faild" => "Title cannot be deleted. Because title has been installed for employees",
    "delete_branch_faild" => "Branch cannot be deleted. Because branch has been installed for company",
    "not_compensatory_workingDay" => "Create failure. Because the compensatory working day coincides with the working day",
    "delete_job_exist" => "Job cannot be deleted. Because Job has been installed for employees",
    "delete_department_exists" => "Department cannot be deleted. Because department has been installed for employees",
    "delete_kind_of_leave" => "Cannot delete leave type. Because type of leave that has been created",
    "push_notification" => [
        "title" => "Timekeeping reminder",
        "title_overtime" => "Overtime application",
        "title_leave_application" => "Leave application",
        "title_request_change_timesheets" => "Request change timesheet application",
        "title_compensatory_leave" => "Compensatory leave application",
        "have_checked_in" => "You checked in at ",
        "please_check_out" => "Please check out before leaving",
        "five_minutes_before_check_in" => "It's almost time to start work, please check in",
        "five_minutes_after_check_out" => "You haven't clocked in today, please create an application to change your working time to supplement your time",
        "created" => [
            "leave_application" => "leave application has been created",
            "over_time" => "overtime order has been created",
            "request_change_timesheets" => "created an application to change working hours",
            "compensatory_leave" => "created an application for compensatory leave"
        ],
        "updated" => [
            "leave_application" => "updated leave application",
            "over_time" => "updated the overtime order",
            "request_change_timesheets" => "updated the application to change working hours",
            "compensatory_leave" => "updated the application for compensatory leave"
        ],
        "accept" => [
            "leave_application" => "your leave request has been approved",
            "over_time" => "your request for overtime has been approved",
            "request_change_timesheets" => "your application to change working hours has been approved",
            "compensatory_leave" => "your request for leave has been approved"
        ],
        "rejected" => [
            "leave_application" => "your leave application has been rejected",
            "over_time" => "your request for overtime has been rejected",
            "request_change_timesheets" => "your application to change working hours has been rejected",
            "compensatory_leave" => "your application for compensatory leave has been denied"
        ]
    ],
    "import" => [
        "employee" => [
            "first_name" => "The field 'first name' cannot be empty",
            "last_name" => "The field 'last name' cannot be empty",
            "sex" => "The field 'sex' cannot be empty",
            "birthday" => "The field 'birthday' cannot be empty",
            "nickname" => "The field 'nickname' cannot be empty",
            "marital_status" => "The field 'marital status' cannot be empty",
            "email" => "The field 'email' cannot be empty",
            "email_format" => "The field 'email' is not in the correct format",
            "phone_number" => "The field 'phone_number' cannot be empty",
            "status" => "The field 'work status' cannot be empty",

            "employee_code" => "The field 'employee code' cannot be empty",
            "branch" => "The field 'branch' cannot be empty",
            "department" => "The field 'department' cannot be empty",
            "position" => "The field 'position' cannot be empty",
            "title" => "The field 'title' cannot be empty",
            "country" => "The field 'country' cannot be empty",
            "date_start_work" => "The field 'start date of work' cannot be empty",
            "user_email" => "The field 'user email' cannot be empty",
            "user_name" => "The field 'user account' cannot be empty",
            "password" => "The field 'password' cannot be empty",
            "role" => "The field 'user group' cannot be empty",
            "exists" => "Invalid value",
            "wrong_format" => "Incorrect date format, please use Y-m-d or d/m/Y",
            "date_format" => "Incorrect format requires format dd-mm-YY",
            'phone_format' => "Incorrect phone number format",
            'is_number' => "Value must be a number",
            'max_value_80' => "Maximum value is 80 characters",
            'max_value_100' => "Maximum value is 100 characters",
            "id_no_format" => "The id no field is not in the correct format",
            "sex" => [
                "male" => "Male",
                "female" => "Female",
            ],
            "martial_status" => [
                "single" => "Single",
                "married" => "Married"
            ],
            "birthday_before_or_equal" => "Ngày sinh không được quá ngày hiện tại",
            "issued_date_before_or_equal" => "Issued date must be less than or equal to the current date",
            "id_expire_after_issued_date" => "Expiration date must be greater than issue date",
            "official_date_after_employee_date" => "The official working date must be greater than or equal to the starting date of work"
        ],
        "max" => [
            "name" => "Maximum name is 255 characters"
        ],
        "required" => [
            "name_kind_of_leave" => "Leave type name must be filled in",
            "symbol_kind_of_leave" => "The leave date designation must be filled out",
            "type" => "Leave type must be filled out",
            "sex" => "Sex must be filled out",
            "martial_status" => "Martial status must be filled out",
            "id_no" => "Id No cannot be left blank",
            "issued_date" => "Date of issue cannot be left blank",
            "issued_by" => "Place of issue cannot be left blank",
            "id_expire" => "Expiration date must not be overlooked",
            "province" => "Province cannot be left blank",
            "ward" => "Ward cannot be left blank",
            "district" => "District cannot be left blank",
            "address" => "Address cannot be left blank"
        ],
        "in" => [
            "type_kind_of_leave" => "The type of leave is not a given option"
        ],
    ],
    "excel_exits" => "Data already exists in the excel file"
];
