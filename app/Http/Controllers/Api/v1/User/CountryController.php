<?php

namespace App\Http\Controllers\Api\v1\User;

use App\Http\Controllers\Api\v1\BaseController;
use App\Http\Services\v1\Admin\CountryService;

class CountryController extends BaseController
{
    /**
     * Instantiate a new controller instance.
     *
     * @param CountryService $countryService
     */
    public function __construct(CountryService $countryService)
    {
        $this->service = $countryService;
    }
}
