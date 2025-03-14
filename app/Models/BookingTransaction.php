<?php

namespace App\Models;

use App\Models\CarStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingTransaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'trx_id',
        'phone_number',
        'is_paid',
        'proof',
        'total_amount',
        'car_store_id',
        'car_service_id',
        'started_at',
        'time_at'
    ];

    // Cast to date
    protected $casts = [
        'started_at' => 'date'
    ];

    // Generate unique trx_id
    public static function generateUniqueTrxId()
    {
        $prefix = 'CC';
        do {
            $randomString = $prefix . mt_rand(1000, 9999);
        } while (self::where('trx_id', $randomString)->exists());

        return $randomString;
    }

    // M:1 to CarStore
    public function service_details(): BelongsTo
    {
        return $this->belongsTo(CarService::class, 'car_service_id');
    }

    // M:1 to CarStore
    public function store_details(): BelongsTo
    {
        return $this->belongsTo(CarStore::class, 'car_store_id');
    }  
}
