<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Services\v1\Admin\CountryService;

class CountryController extends BaseMasterController
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
