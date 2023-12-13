<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Services\v1\User\ManagerCompensatoryLeaveService;
use App\Http\Services\v1\User\ManagerLeaveService;
use App\Http\Services\v1\User\ManagerOvertimeService;
use App\Http\Services\v1\User\ManagerRequestChangeTimesheetService;
use Illuminate\Http\Request;

class ManagerController extends BaseController
{
    const ACTION = [
        'ACCEPT' => 'accept',
        'REJECT' => 'reject',
        'REJECT-AFTER-ACCEPT' => 'reject-after-accept',
    ];
    /**
     * @var ManagerLeaveService
     */
    protected $managerLeaveService;
    /**
     * @var ManagerOvertimeService
     */
    protected $managerOvertimeService;
    /**
     * @var ManagerRequestChangeTimesheetService
     */
    protected $managerRequestChangeTimesheetService;
    /**
     * @var ManagerCompensatoryLeaveService
     */
    protected $managerCompensatoryLeaveService;

    /**
     * @param ManagerLeaveService $managerLeaveService
     * @param ManagerOvertimeService $managerOvertimeService
     * @param ManagerRequestChangeTimesheetService $managerRequestChangeTimesheetService
     * @param ManagerCompensatoryLeaveService $managerCompensatoryLeaveService
     */
    public function __construct(
        ManagerLeaveService $managerLeaveService,
        ManagerOvertimeService $managerOvertimeService,
        ManagerRequestChangeTimesheetService $managerRequestChangeTimesheetService,
        // ManagerCompensatoryLeaveService $managerCompensatoryLeaveService
    ) {
        $this->managerLeaveService = $managerLeaveService;
        $this->managerOvertimeService = $managerOvertimeService;
        $this->managerRequestChangeTimesheetService = $managerRequestChangeTimesheetService;
        // $this->managerCompensatoryLeaveService = $managerCompensatoryLeaveService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function getApprovers(Request $request)
    {
        switch ($request->type) {
            case config('manager_application.type_leave'):
                return $this->managerLeaveService->getApprovers();
            case config('manager_application.type_overtime'):
                return $this->managerOvertimeService->getApprovers();
            case config('manager_application.type_request_change_timesheet'):
                return $this->managerRequestChangeTimesheetService->getApprovers();
            case config('manager_application.type_compensatory_leave'):
                return $this->managerCompensatoryLeaveService->getApprovers();
            default:
                break;
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function handleForm(Request $request, $id)
    {
        switch ($request->type) {
            case config('manager_application.type_leave'):
                return $this->managerLeaveService->handleForm($request, $id);
            case config('manager_application.type_overtime'):
                return $this->managerOvertimeService->handleForm($request, $id);
            case config('manager_application.type_request_change_timesheet'):
                return $this->managerRequestChangeTimesheetService->handleForm($request, $id);
            case config('manager_application.type_compensatory_leave'):
                return $this->managerCompensatoryLeaveService->handleForm($request, $id);
            default:
                break;
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function index(Request $request)
    {
        switch ($request->type) {
            case config('manager_application.type_leave'):
                return $this->managerLeaveService->index($request);
            case config('manager_application.type_overtime'):
                return $this->managerOvertimeService->index($request);
            case config('manager_application.type_request_change_timesheet'):
                return $this->managerRequestChangeTimesheetService->index($request);
            case config('manager_application.type_compensatory_leave'):
                return $this->managerCompensatoryLeaveService->index($request);
            default:
                break;
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|void
     */
    public function show(Request $request, $id)
    {
        switch ($request->type) {
            case config('manager_application.type_leave'):
                return $this->managerLeaveService->show($request, $id);
            case config('manager_application.type_overtime'):
                return $this->managerOvertimeService->show($request, $id);
            case config('manager_application.type_request_change_timesheet'):
                return $this->managerRequestChangeTimesheetService->show($request, $id);
            case config('manager_application.type_compensatory_leave'):
                return $this->managerCompensatoryLeaveService->show($request, $id);
            default:
                break;
        }
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse|mixed|void
     */
    public function update(Request $request, $id)
    {
        switch ($request->type) {
            case config('manager_application.type_leave'):
                return $this->managerLeaveService->_update($request, $id);
            case config('manager_application.type_overtime'):
                return $this->managerOvertimeService->_update($request, $id);
            case config('manager_application.type_request_change_timesheet'):
                return $this->managerRequestChangeTimesheetService->_update($request, $id);
            case config('manager_application.type_compensatory_leave'):
                return $this->managerCompensatoryLeaveService->_update($request, $id);
            default:
                break;
        }
    }
}
