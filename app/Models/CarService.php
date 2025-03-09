<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarService extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name',
        'slug',
        'price',
        'about',
        'photo',
        'duration_in_hour'
    ];

    // Set the name attribute and slug attribute
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value);
    }

    // 1:M to StoreService
    public function storeServices(): HasMany
    {
        return $this->hasMany(StoreService::class, 'car_service_id');
    }
}
