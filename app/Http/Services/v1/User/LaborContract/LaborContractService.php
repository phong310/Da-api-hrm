<?php

namespace App\Http\Services\v1\User\LaborContract;

use App\Http\Services\v1\BaseService;
use App\Models\LaborContract\LaborContract;
use App\Repositories\Interfaces\LaborContract\LaborContractInterface;
use App\Transformers\LaborContract\LaborContractTransformer;
use App\Transformers\LaborContract\LaborContractAllowanceTransForm;
use App\Transformers\LaborContract\LaborContractTransformerMySelf;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class LaborContractService extends BaseService
{
    /**
     * @var LaborContractInterface
     */
    protected $laborContract;

    public function __construct(LaborContractInterface $laborContract)
    {
        $this->laborContract = $laborContract;

        parent::__construct();
    }

    public function setModel()
    {
        $this->model = new LaborContract();
    }

    public function getLaborContractMySelf(Request $request)
    {
        $user = $request->user();
        $employeeId = $user->employee_id;

        $data = $this->laborContract->getAllMySelf($employeeId);
        return $this->setTransformersMySelf($data);
    }

    public function showMySelf(Request $request, $id)
    {
        $user = $request->user();
        $employeeId = $user->employee_id;
        $data = $this->laborContract->getDetailMySelf($employeeId, $id);

        if ($data) {
            $transformedData = $this->setTransformDetail($data);
            return $transformedData;
        } else {
            return response()->json(['message' => 'Data not found'], 404);
        }
    }


    public function setTransformDetail($data)
    {
        $transForm = new LaborContractAllowanceTransForm();
        $result = $transForm->transformData($data);
        return $result;
    }


    public function setTransformers($data)
    {
        $collection = $data->getCollection();
        $instances = collect($collection)->transformWith(new LaborContractTransformer())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $instances;
    }

    public function setTransformersMySelf($data)
    {
        $collection = $data->getCollection();
        $instances = collect($collection)->transformWith(new LaborContractTransformerMySelf())
            ->paginateWith(new IlluminatePaginatorAdapter($data));

        return $instances;
    }


    public function appendFilter()
    {
        $is_expired = $this->request->get('is_expired');
        $is_expiring = $this->request->get('is_expiring');
        $employee_full_name = $this->request->get('full_name');
        $labor_contract_type_id = $this->request->get('labor_contract_types');
        $now = Carbon::now();

        if (!is_null($labor_contract_type_id)) {
            $this->query->where('labor_contract_type_id', $labor_contract_type_id);
        }

        $date_now = Carbon::now();

        if ($employee_full_name) {
            $this->query->whereHas('employee.personalInformation', function ($q) use ($employee_full_name) {
                $q->whereRaw(
                    "TRIM(CONCAT(first_name, ' ', last_name)) like '%{$employee_full_name}%'"
                );
            });
        }

        if ($is_expired) {
            $this->query->whereIn('status', [
                LaborContract::STATUS['EXTEND'],
                LaborContract::STATUS['TERMINATE'],
                LaborContract::STATUS['EXPIRTION'],
            ]);
        } else if ($is_expiring) {
            $this->query->where(function ($query) use ($now) {
                $query->where('status', LaborContract::STATUS['ACTIVE'])
                    ->where('expire_date', '<=', $now->addDays(LaborContract::EXPIRATION_DAY));
            });
        } else {
            $this->query->whereDate('expire_date', '>=', $date_now);
            $this->query->whereIn('status', [LaborContract::STATUS['ACTIVE'], LaborContract::STATUS['POSTPONE']]);
        }
        $this->query->with(['position', 'branch', 'employee.personalInformation', 'labor_contract_type']);
    }


    public function store(Request $request)
    {
        $data = $request->only($this->model->getFillable());
        $companyId = $this->getCompanyId();
        $data['company_id'] = $companyId;

        return $this->laborContract->store($data);
    }

    public function show(Request $request, $id)
    {

        $relations = ['allowances', 'addresses', 'employee.position', 'employee.department', 'employee.branch'];

        $laborContract = $this->laborContract->show($id, $relations);

        return $this->transformData($laborContract);
    }


    public function transformData($laborContract)
    {
        $data = array_merge([], $laborContract->toArray());
        unset($data['created_at']);
        unset($data['updated_at']);

        $data['allowances'] = Arr::pluck($laborContract['allowances'], 'allowance_id');

        // Xử lý position và personal_information
        unset($data['employee']['position']['created_at']);
        unset($data['employee']['position']['updated_at']);
        $data['employee']['position'] = [
            'name' => $laborContract['employee']['position']['name']
        ];


        unset($data['employee']['branch_id']);
        unset($data['employee']['card_number']);
        unset($data['employee']['company_id']);
        unset($data['employee']['created_at']);
        unset($data['employee']['updated_at']);
        unset($data['employee']['date_start_work']);
        unset($data['employee']['deleted_at']);
        unset($data['employee']['position_id']);
        unset($data['employee']['department_id']);
        unset($data['employee']['personal_information_id']);
        unset($data['employee']['official_employee_date']);
        unset($data['employee']['id']);

        $data['employee']['personal_information'] = [
            'full_name' => $laborContract['employee']['personalInformation']['full_name'] ?? "",
            'email' => $laborContract['employee']['personalInformation']['email'] ?? "",
            'birthday' => $laborContract['employee']['personalInformation']['birthday'] ?? "",
            'phone' => $laborContract['employee']['personalInformation']['phone'] ?? "",
            'sex' => $laborContract['employee']['personalInformation']['sex'] ?? "",
        ];

        unset($data['employee']['department']['created_at']);
        unset($data['employee']['department']['update_at']);
        $data['employee']['department'] = [
            'name' => $laborContract['employee']['department']['name'] ?? "",
        ];

        unset($data['employee']['branch']['created_at']);
        unset($data['employee']['branch']['update_at']);
        $data['employee']['branch'] = [
            'name' => $laborContract['employee']['branch']['name'] ?? "",
        ];



        foreach ($data['addresses'] as $key => $address) {
            unset($address['created_at']);
            unset($address['updated_at']);

            $data['addresses'][$key] = $address;
        }

        return $data;
    }



    public function update(Request $request, $id)
    {
        $data = $request->only($this->model->getFillable());

        return $this->laborContract->update($data, $id);
    }

    public function countByEmployee($employeeId)
    {
        return $this->model::query()->where('employee_id', $employeeId)->count();
    }

    public function hasLaborContractActive($employeeId)
    {
        $laborContract = $this->query->where(['employee_id' => $employeeId])
            ->whereIn('status', [LaborContract::STATUS['ACTIVE'], LaborContract::STATUS['EXTEND']])
            ->first();

        return (bool)$laborContract;
    }
}
