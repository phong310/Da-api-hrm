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
    'not_found' => 'Không tìm thấy',
    'timekeeping_success' => 'Chấm công thành công',
    'created_success' => 'Đã thêm mới thành công',
    'update_success' => 'Đã cập nhật thành công',
    'delete_success' => 'Đã xóa thành công',
    'not_delete' => 'Không được xóa',
    'not_delete_salay_in_await' => 'Không xóa được bảng lương ở trạng thái chờ phê duyệt',
    'not_delete_salary_in_confirmed' => 'Không xóa được bảng lương ở trạng thái đã duyệt',
    'not_delete_salary_in_public' => 'Không xóa được bảng lương ở trạng thái công khai',
    'sync_success' => 'Dữ liệu đang đồng bộ vui lòng chờ',
    'restore_success' => 'Đã khôi phục thành công',
    'approved_successfully' => 'Phê duyệt thành công',
    'invalid_data' => 'Dữ liệu không hợp lệ',
    'request_approved_successfully' => 'Gửi phê duyệt thành công',
    'rejected_successfully' => 'Từ chối thành công',
    'working_day_exits' => 'Ngày làm việc đã tồn tại',
    'data_exits' => 'Dữ liệu đã tồn tại',
    'departments' => 'Tên bộ phận',
    'branchs' => 'Tên chi nhánh',
    'server_error' => 'Hệ thống đang bảo trì!',
    'company_id' => 'Công ty',
    'not_active' => 'Công ty đang được kiểm duyệt, vui lòng thử lại sau',
    'employee_quits' => 'Tài khoản nhân viên đã nghỉ việc !',
    'kind_leave_id' => 'loại nghỉ phép',
    'reason' => 'lý do',
    "basic_salary" =>  'Lương cơ bản',
    "hourly_wages" => 'Lương theo giờ',
    "taxable_income" => 'Thu nhập chịu thuế',
    "personal_income_tax" => 'Thuế thu nhập cá nhân',
    "syndicate_money" => 'Qũy công đoàn',

    'start_time' => 'thời gian bắt đầu',
    'end_time' => 'thời gian kết thúc',
    'approver_id' => 'người phê duyệt',
    'approver_id_1' => 'người phê duyệt 1',
    'approver_id_2' => 'người phê duyệt 2',
    'note' => 'ghi chú',
    'name_other_allowance' => "Tên phụ cấp không được để trống !",
    "name_amount_of_money" => "Lương thưởng không được để trống !",
    'current_password' => 'Mật khẩu không đúng.',
    'exists_time_application' => 'Ngày :value đã tồn tại khoảng thời gian này. Vui lòng chọn khoảng thời gian khác',
    'run_out_of_application' => 'Ngày :value đã tồn tại :application_type. Vui lòng chọn ngày tạo đơn khác ',
    'not_in_working_day_date' => 'Ngày :value không thuộc thời gian làm việc',
    'not_in_working_day' => 'Trường :attribute không thuộc thời gian làm việc',
    'is_form_processed' => 'Đơn của bạn đã được xử lý',
    'not_permission' => 'Không có quyền',
    'not_all_approved_salaries' => 'Bạn cần chốt lương tất cả các nhân viên',
    'not_all_approved_checked' => 'Có nhân viên chưa được kiểm tra , vui lòng kiểm tra lương',
    'email_not_found' => 'Email của bạn không tồn tại. Vui lòng đăng ký email',
    'not_overtime_in_working_day' => 'Thời gian OT trùng với thời gian làm việc.',
    'not_create_leave_form' => 'Số ngày phép của bạn không đủ, vui lòng chuyển sang Nghỉ không lương hoặc kiểm tra lại các đơn chờ phê duyệt.',
    'email_reset_pass' => [
        'subject' => 'Đặt lại mật khẩu HRM!',
        'greeting' => 'Đặt lại mật khẩu HRM!',
        'content' => 'Bạn nhận được email này vì chúng tôi đã nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn.',
        'content_if' => 'Nếu bạn không yêu cầu đặt lại mật khẩu, bạn không cần thực hiện thêm hành động nào.',
        'reset_password' => 'Đặt lại mật khẩu'
    ],
    'create_company' => [
        'subject' => 'Tạo mới công ty',
        'greeting' => 'Tạo mới công ty',
        'content' => 'Bạn đã đăng ký thành công cho công ty của bạn. Vui lòng đợi quản trị viên phê duyệt công ty của bạn',
    ],
    'active_company' => [
        'subject' => 'Công ty đã được phê duyệt!',
        'greeting' => 'Công ty đã được phê duyệt!',
        'content' => 'Công ty của bạn đã được phê duyệt thành công. Vui lòng ấn vào nút bên dưới để thêm mới thông tin nhân viên.',
        'create_employee' => 'Thêm thông tin nhân viên'
    ],
    'send_mail_success' => 'Chúng tôi đã gửi qua e-mail liên kết đặt lại mật khẩu của bạn!',
    'token_invalid' => 'Mã thông báo đặt lại mật khẩu này không hợp lệ',
    'reset_pass_success'  => 'Thay đổi mật khẩu thành công!',
    'overtime_overlaps' => 'Thời gian tăng ca đã bị trùng',
    "overtime_exist" => "Thời gian không thuộc thời gian OT",
    'validate' => [
        'working_day' => [
            'end_time_after_start_time' => 'Trường giờ kết thúc làm việc phải sau trường giờ bắt đầu làm việc',
            'end_lunch_break_after_start_lunch_break' => 'Trường giờ kết thúc nghỉ trưa phải sau trường giờ bắt đầu nghỉ trưa',
        ],
        'required' => [
            'day_in_week_id' => 'Trường ngày làm việc trong tuần phải được điền',
            'time_zone' => 'Trường múi giờ phải được điền',
            'format_date' => 'Trường định dạng ngày phải được điền',
            'locale' => 'Trường ngôn ngữ phải được điền',
            "note" => "test",
            "company" => [
                "name" => "Trường tên công ty phải được điền",
                "address" => "Trường địa chỉ công ty phải được điền",
                "phone_number" => "Trường số điện thoại công ty phải được điền",
                "tax_code" => "Trường mã số thuế phải được điền",
                "representative" => "Trường người đại diện phải được điền",
                "type_of_business" => "Trường loại hình kinh doanh phải được điền"
            ]
        ],
        'unique' => [
            "company" => [
                "name" => "Tên công ty đã tồn tại",
                "tax_code" => "Mã số thuế đã tồn tại",
                "phone_number" => "Số điện thoại đã tồn tại",
            ]
        ]
    ],
    'labor_contract_exits' => 'Hợp đồng lao đồng đã tồn tại',
    'labor_contract_code_exits' => 'Mã hợp đồng đã tồn tại',
    "labor_expire_date" => 'Trường ngày hết hạn phải lớn hơn ngày có hiệu lực !',
    "labor_sign_date" => 'Trường ngày hết hạn phải lớn hơn ngày ký !',
    'personal_in_come_tax_exits' => 'Giá trị được nhập phải lớn hơn giá trị trước đó !',
    'percent_personal_in_come_tax_exits' => 'Trường thuế suất phải có giá trị lớn hơn trước đó !',
    'salary_sheets_date_exists' => "Khoảng thời gian này đã tồn tại. Vui lòng chọn khoảng thời gian khác",
    "labor_current_date" => "Trường ngày hết hạn phải lớn hơn ngày hiện tại !",
    "salary_closing" => "Lương đã chốt không thể thay đổi",
    "other_amount_allowance_exits" => "Tên phụ cấp đã tồn tại!",
    "unknown" => "Không xác định",
    "time_lunch_break_exist" => "Thời gian giờ nghỉ trưa phải nằm trong khoảng thời gian kết thúc và thời gian bắt đầu !",
    "validate_to_date" => "Ngày kết thúc phải lớn hơn ngày bắt đầu",
    "validate_school_name" => "Tên trường học không được vượt quá 50 ký tự",
    "percent_valid" => "Trường này phải nằm trong khoảng 0 - 1000",
    "import_fails" => "Tải lên thất bại do có các bản ghi trùng nhau",
    "import_success" => "Tải lên thành công",
    "personal_income_tax" => [
        "min_value" => "Giá trị nhỏ nhất của thuế thu nhập cá nhân không được bỏ trống",
        "percent" => "Phần trăm thuế suất không được bỏ trống"
    ],
    "school_name_empty" => "Tên trường phải được nhập",
    "validate_descriptions" => "Trường mô tả phải được nhập",
    "delete_labor_contract_type_exist" => "Không xóa được loại hợp đồng đang sử dụng !",
    'end_time_after_start_time' => 'Trường thời gian kết thúc phải lớn hơn thời gian bắt đầu',
    "leave_form_holiday_exist" => "Khoảng thời gian xin nghỉ đang thuộc khoảng thời gian nghỉ lễ !",
    "delete_labor_contract_type_exist" => "Không xóa được loại hợp đồng đang sử dụng !",
    "delete_allowance_exist" => "Không xóa được phụ cấp đang được sử dụng !",
    "delete_position_faild" => "Không xóa được vị trí. Do vị trí đã được cài đặt cho nhân viên",
    "delete_department_faild" => "Không xóa được bộ phận. Do bộ phận đã được cài đặt cho nhân viên",
    "delete_title_faild" => "Không xóa được chức vụ. Do chức vụ đã được cài đặt cho nhân viên",
    "delete_branch_faild" => "Không xóa được chi nhánh. Do chi nhánh đã được cài đặt cho công ty",
    "not_compensatory_workingDay" => "Tạo thất bại. Do ngày làm bù trùng với ngày làm việc",
    "delete_job_exist" => "Không xóa được công việc . Do công việc đã được cài đặt cho nhân viên",
    "delete_department_exists" => "Không xóa được bộ phận . Do bộ phận đã được cài đặt cho nhân viên",
    "delete_kind_of_leave" => "Không xóa được loại nghỉ phép. Do loại nghỉ phép đã được tạo đơn",
    "push_notification" => [
        "have_checked_in" => "Bạn đã check in lúc ",
        "please_check_out" => "Vui lòng check out trước khi ra về",
        "title" => "Nhắc nhở chấm công",
        "title_overtime" => "Đơn tăng ca",
        "title_leave_application" => "Đơn xin nghỉ",
        "title_request_change_timesheets" => "Đơn thay đổi thời gian làm việc",
        "title_compensatory_leave" => "Đơn xin nghỉ bù",
        "five_minutes_before_check_in" => "Hôm nay bạn chưa chấm công , vui lòng chấm công sớm",
        "five_minutes_after_check_out" => "Hôm nay bạn chưa chấm công, vui lòng tạo đơn thay đổi thời gian làm việc để bổ sung công",
        "created" => [
            "leave_application" => "đã tạo đơn xin nghỉ",
            "over_time" => "đã tạo đơn tăng ca",
            "request_change_timesheets" => "đã tạo đơn thay đổi thời gian làm việc",
            "compensatory_leave" => "đã tạo đơn xin nghỉ bù"
        ],
        "updated" => [
            "leave_application" => "đã cập nhật đơn xin nghỉ",
            "over_time" => "đã cập nhật đơn tăng ca",
            "request_change_timesheets" => "đã cập nhật đơn thay đổi thời gian làm việc",
            "compensatory_leave" => "đã cập nhật đơn xin nghỉ bù"
        ],
        "accept" => [
            "leave_application" => "đã phê duyệt đơn xin nghỉ của bạn",
            "over_time" => "đã phê duyệt đơn tăng ca của bạn",
            "request_change_timesheets" => "đã phê duyệt đơn thay đổi thời gian làm việc của bạn",
            "compensatory_leave" => "đã phê duyệt đơn xin nghỉ bù của bạn"
        ],
        "rejected" => [
            "leave_application" => "đã từ chối đơn xin nghỉ của bạn",
            "over_time" => "đã từ chối đơn tăng ca của bạn",
            "request_change_timesheets" => "đã từ chối đơn thay đổi thời gian làm việc của bạn",
            "compensatory_leave" => "đã từ chối đơn xin nghỉ bù của bạn"
        ]
    ],
    "import" => [
        "employee" => [
            "first_name" => "Trường họ không được bỏ trống",
            "last_name" => "Trường tên không được bỏ trống",
            "sex" => 'Trường giới tính không được bỏ trống',
            "birthday" => "Trường ngày sinh không được bỏ trống",
            "nickname" => "Trường biệt danh không được bỏ trống",
            "marital_status" => "Trường tình trạng hôn nhân không được bỏ trống",
            "email" => "Trường email không được bỏ trống",
            "email_format" => "Trường email không đúng định dạng",
            "phone_number" => "Trường số điện thoại không được bỏ trống",
            "status" => "Trường trạng thái làm việc",

            "employee_code" => "Trường mã nhân viên không được bỏ trống",
            "branch" => "Trường chi nhánh không được bỏ trống",
            "department" => "Trường bộ phận không được bỏ trống",
            "position" => "Trường vị trí không được bỏ trống",
            "title" => "Trường chức vụ không được bỏ trống",
            "country" => "Trường quốc tịch không được bỏ trống",
            "date_start_work" => "Trường ngày bắt đầu làm việc không được bỏ trống",
            "user_email" => "Trường email người dùng không được để trống",
            "user_name" => "Trường tài khoản không được bỏ trống",
            "password" => "Trường password không được để trống",
            "role" => "Trường nhóm người dùng không được để trống",
            "exists" => "Giá trị không hợp lệ",
            "wrong_format" => "Sai định dạng date , yêu cầu định dạng Y-m-d hoặc d/m/Y",
            "date_format" => "Không đúng định dạng , yêu cầu định dạng dd-mm-YY",
            'phone_format' => "Không đúng định dạng số điện thoại",
            'is_number' => "Giá trị phải là một số",
            'max_value_80' => "Giá trị tối đa 80 kí tự",
            'max_value_100' => "Giá trị tối đa 100 kí tự",
            "id_no_format" => "Số căn cước công dân không đúng định dạng",
            "sex" => [
                "male" => "Nam",
                "female" => "Nữ"
            ],
            "martial_status" => [
                "single" => "Độc thân",
                "married" => "Đã kết hôn"
            ],
            "birthday_before_or_equal" => "Ngày sinh không được quá ngày hiện tại",
            "issued_date_before_or_equal" => "Ngày cấp phải nhỏ hơn hoặc bằng ngày hiện tại",
            "id_expire_after_issued_date" => "Ngày hết hạn phải lớn hơn ngày cấp",
            "official_date_after_employee_date" => "Ngày làm việc chính thức phải lớn hơn hoặc bằng ngày bắt đầu làm việc"
        ],
        "max" => [
            "name" => "Tên tối đa là 255 kí tự"
        ],
        "required" => [
            "name_kind_of_leave" => "Tên loại nghỉ phép phải được điền",
            "symbol_kind_of_leave" => "Kí hiệu ngày nghỉ phép phải được điền",
            "type" => "Loại nghỉ phép phải được điền",
            "sex" => "Giới tính không được bỏ trống",
            "martial_status" => "Tình trạng kết hôn không được bỏ trống",
            "id_no" => "Số CCCD không được bỏ trống",
            "issued_date" => "Ngày cấp không được bỏ trống",
            "issued_by" => "Nơi cấp không được bỏ trống",
            "id_expire" => "Ngày hết hạn không được bỏ trông",
            "province" => "Tỉnh/Thành phố không được bỏ trống",
            "ward" => "Xã/Phường không được bỏ trống",
            "district" => "Quận/Huyện không được bỏ trống",
            "address" => "Địa chỉ không được bỏ trống"
        ],
        "in" => [
            "type_kind_of_leave" => "Loại nghỉ phép không thuộc lựa chọn cho trước"
        ],
    ],
    "excel_exits" => "Dữ liệu đã tồn tại trong file excel"
];
