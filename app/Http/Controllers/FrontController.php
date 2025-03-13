<?php

namespace App\Http\Controllers;

use App\Models\CarService;
use App\Models\City;

class FrontController extends Controller
{
    //
    public function index()
    {
        $cities = City::all(); // get all cities
        $services = CarService::withCount(['storeServices'])->get(); // get all services with count of store services

        return view('front.index', compact('cities', 'services'));
    }
}
