<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreOrUpdateIdenCardEmployeeRequest;
use App\Http\Services\v1\User\IdentificationService;
use App\Http\Services\v1\Admin\IdentificationCardService;
use Illuminate\Http\Request;

class IdentificationController extends Controller
{
    /**
     * @param IdentificationService $identificationService
     */

    public function __construct(IdentificationService $identificationService)
    {
        $this->service = $identificationService;
    }

    public function identificationCard()
    {
        return $this->service->identificationCard();
    }

    public function updateIdentification(Request $request)
    {
        return $this->service->updateIdentification($request);
    }
}
