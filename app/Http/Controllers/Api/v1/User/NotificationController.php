<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Services\v1\User\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @param notificationService $peopleService
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->service = $notificationService;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(Request $request)
    {
        return $this->service->index($request);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        return $this->service->store($request);
    }

    /**
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function show(Request $request, $id)
    {
        return $this->service->_show($request, $id);
    }

    /**
     * @param Request $request
     */
    public function markAsSeen(Request $request)
    {
        return $this->service->markAsSeen($request);
    }

    /**
     * @param Request $request
     * @return int
     */
    public function newCount(Request $request)
    {
        return $this->service->newCount($request);
    }

    /**
     * @param Request $request
     * @param $id
     */
    public function markAsRead(Request $request, $id)
    {
        return $this->service->markAsRead($request, $id);
    }

    /**
     * @param Request $request
     */
    public function markAllAsRead(Request $request)
    {
        return $this->service->markAllAsRead($request);
    }
}
