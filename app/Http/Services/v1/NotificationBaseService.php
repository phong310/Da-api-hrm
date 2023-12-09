<?php

namespace App\Http\Services\v1;

use App\Models\Notification;
use App\Transformers\NotificationTransformer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

abstract class NotificationBaseService
{
    /** @var Model */
    protected $model;

    /** @var Builder */
    protected $query;

    public function __construct()
    {
        $this->request = request();
        $this->setModel();
        $this->setQuery();
    }

    protected function setQuery()
    {
        $this->query = $this->model->query();
    }

    /**
     * @return mixed
     */
    abstract protected function setModel();

    public function index(Request $request)
    {
        $data = [];
        $per_page = $request->per_page ?: 20;
        $filter = $request->filter;
        $types = $request->get('type');
        $receiver_id = $request->user()->employee_id;

        $query = $this->query->where('receiver_id', $receiver_id)
            ->with('sender.personalInformation')
            ->orderBy('created_at', 'DESC');

        if ($filter == 'unread') {
            $query->where('status', Notification::STATUS['UNREAD']);
        }

        if ($types !== null) {
            if (!empty($types)) {
                $query->whereIn('type', $types);
            }
        }

        $data = $query->paginate($per_page);

        if (method_exists($this, 'setTransformers')) {
            $data = $this->setTransformers($data);
        }

        return $data;
    }

    public function _store(Request $request, $message = '')
    {
        $data = $request->all();

        $result = $this->query->create($data);

        return $this->createResultResponse($result, $message);
    }

    public function newCount(Request $request)
    {
        $count = $this->query->where(['receiver_id' => $request->user()->employee_id, 'status' => Notification::STATUS['NEW']])->count();

        return $count;
    }

    public function markAsSeen(Request $request)
    {
        $this->query
            ->where(['receiver_id' => $request->user()->employee_id, 'status' => Notification::STATUS['NEW']])
            ->update(['status' => Notification::STATUS['UNREAD']]);
    }

    public function markAsRead(Request $request, $id)
    {
        $this->query->find($id)->update([
            'status' => Notification::STATUS['READ'],
        ]);
    }

    public function markAllAsRead(Request $request)
    {
        $this->query
            ->where(['receiver_id' => $request->user()->employee_id])
            ->where('status', '<>', Notification::STATUS['READ'])
            ->update(['status' => Notification::STATUS['READ']]);
    }

    public function setTransformers($data)
    {
        $collection = $data->getCollection();

        $paginated = collect($collection)->transformWith(new NotificationTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $paginated;
    }

    protected function successResponse($responseData = null)
    {
        return response()->json($responseData);
    }

    protected function createResultResponse($data, $message = '')
    {
        return $this->successResponse(['message' => $message ?: __('message.created_success'), 'data' => $data]);
    }
}
