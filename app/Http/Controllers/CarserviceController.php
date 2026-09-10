<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CarserviceController extends Controller
{
        public function dashboardcarsevice(Request $request)
{
            $this->requireLogin($request);
    return view('sale.dashboardcarservice');
}
}
