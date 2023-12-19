<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Services\v1\User\LaborContract\LaborContractTypeService;
use Illuminate\Http\Request;

class LaborContractTypeController extends BaseController
{
    /**
     * @var LaborContractTypeService
     */
    protected $laborContractTypeService;

    public function __construct(LaborContractTypeService $laborContractTypeService)
    {
        $this->service = $laborContractTypeService;
    }
}
