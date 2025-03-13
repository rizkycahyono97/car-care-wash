<?php

namespace App\Http\Controllers;

use App\Models\CarService;
use App\Models\CarStore;
use App\Models\City;
use Illuminate\Http\Request;

class FrontController extends Controller
{
    //
    public function index()
    {
        $cities = City::all(); // get all cities
        $services = CarService::withCount(['storeServices'])->get(); // get all services with count of store services

        return view('front.index', compact('cities', 'services'));
    }

    public function search(Request $request)
    {
        // ambil data dari request URL
        $cityId = $request->input('city_id');
        $serviceTypeId = $request->input('service_type');

        // Cari layanan berdasarkan ID 
        $carService = CarService::where('id', $serviceTypeId)->first();
        if (!$carService) {
            return redirect()->back()->with('error', 'Service type not Found');
        }

        // cari toko berdasarkan layanan yang tersedia di kota tertentu
        $stores = CarStore::whereHas('storeServices', function ($query) use ($carService) {
            $query->where('car_service_id', $carService->id);
        })->where('city_id', $cityId)->get();

        // ambil nama kota 
        $city = City::find($cityId);

        // simpan ke dalam session
        session()->put('serviceTypeId', $request->input('service_type'));

        
        return view('front.stores', [
            'stores' => $stores,
            'carService' => $carService,
            'cityName' => $city ? $city->name : 'Unknown City'
        ]);
    }

    public function details(CarStore $carStore)
    {
        // ambil data dari session untuk details
        $serviceTypeId = session()->get('serviceTypeId');
        $carService = CarService::where('id', $serviceTypeId)->first();

        return view('front.details', compact('carStore', 'carService'));
    }
}
