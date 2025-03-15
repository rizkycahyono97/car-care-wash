<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\City;
use App\Models\CarStore;
use App\Models\CarService;
use Illuminate\Http\Request;
use App\Models\BookingTransaction;
use App\Http\Requests\StoreBookingRequest;
use App\Http\Requests\StoreBookingPaymentRequest;

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

    public function booking(CarStore $carStore)
    {
        session()->put('carStoreId', $carStore->id);

        $serviceTypeId = session()->get('serviceTypeId');
        $service = CarService::where('id', $serviceTypeId)->first();

        return view('front.booking', compact('carStore', 'service'));
    }

    // Store booking validasi dari StoreBookingRequest laravel
    public function booking_store(StoreBookingRequest $request)
    {
        $customerName  = $request->input('name');
        $customerPhoneNumber = $request->input('phone_number');
        $customerTimeAt = $request->input('time_at');

        // simpan ke dalam session
        session()->put('customerName', $customerName);
        session()->put('customerPhoneNumber', $customerPhoneNumber);
        session()->put('customerTimeAt', $customerTimeAt);

        // dd(session()->all());

        // ambil data dari session 
        $serviceTypeId = session()->get('serviceTypeId');
        $carStoreId = session()->get('carStoreId');

        // dd($carStoreId, $serviceTypeId);

        return redirect()->route('front.booking.payment', [$carStoreId, $serviceTypeId]);
    }

    // 
    public function booking_payment(CarStore $carStore, CarService $carService)
    {
        $ppn = 0.11;
        $totalPpn = $ppn * $carService->price;
        $bookingFee = 25000;
        $totalGrandTotal = $totalPpn + $bookingFee + $carService->price;

        // dd(number_format($totalGrandTotal, 0, ',', '.'));

        session()->put('totalAmount', $totalGrandTotal);
        
        return view('front.payment', compact('carService', 'carStore', 'totalPpn', 'bookingFee', 'totalGrandTotal',));
    }

    // simpan ke database
    public function booking_payment_store(StoreBookingPaymentRequest $request)
    {
        // dd(session()->all());

        $customerName = session()->get('customerName', 'Guest');
        $customerPhoneNumber = session()->get('customerPhoneNumber');
        $totalAmount = session()->get('totalAmount');
        $customerTimeAt = session()->get('customerTimeAt');
        $serviceTypeId = session()->get('serviceTypeId');
        $carStoreId = session()->get('carStoreId');

        $bookingTransactionId = null;

        if ($request->hasFile('proof')) {
            $proofPath = $request->file('proof')->store('proofs', 'public');
            $validated['proof'] = $proofPath;
        }

        $validated['name'] = $customerName;
        $validated['total_amount'] = $totalAmount;
        $validated['phone_number'] = $customerPhoneNumber;
        $validated['started_at'] = Carbon::tomorrow()->format('Y-m-d');
        $validated['time_at'] = $customerTimeAt;
        $validated['car_service_id'] = $serviceTypeId;
        $validated['car_store_id'] = $carStoreId;
        $validated['is_paid'] = false;
        $validated['trx_id'] = BookingTransaction::generateUniqueTrxId();

        $newBooking = BookingTransaction::create($validated);

        $bookingTransactionId = $newBooking->id;

        return redirect()->route('front.success.booking', $bookingTransactionId);
    }

    public function success_booking(BookingTransaction $bookingTransaction)
    {
        return view('front.success_booking', compact('bookingTransaction'));
    }

    public function transactions()
    {
        return view('front.transactions');
    }

    public function transaction_details(Request $request)
    {
        $request->validate([
            'trx_id' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'string', 'max:255']
        ]);

        $trx_id = $request->input('trx_id');
        $phone_number = $request->input('phone_number');

        $details = BookingTransaction::with(['service_details', 'store_details'])
        ->where('trx_id', $trx_id)
        ->where('phone_number', $phone_number)
        ->first();

        if (!$details) {
            return redirect()->back()->withErrors(['error' => 'Transaction not found.']);
        } 

        $ppn = 0.11;
        $totalPpn = $ppn * $details->service_details->price;
        $bookingFee = 25000;

        return view('front.transactions_details', compact('details', 'ppn', 'totalPpn', 'bookingFee'));
    }
}
