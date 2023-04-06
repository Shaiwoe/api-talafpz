<?php

namespace App\Models;

use Carbon\Carbon;
use App\Models\Episode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Course extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'courses';
    protected $guarded = [];

    public function getStatusAttribute($status)
    {
        switch ($status) {
            case '0':
                $status = 'غیر فعال';
                break;
            case '1':
                $status = 'فعال';
                break;
        }
        return $status;
    }

    protected function isSale(): Attribute
    {
        return Attribute::make(
            get: fn () => ($this->quantity > 0 && $this->sale_price != 0 && $this->sale_price != null && $this->date_on_sale_from < Carbon::now() && $this->date_on_sale_to > Carbon::now()) ? true : false,
            set: fn () => ($this->quantity > 0 && $this->sale_price != 0 && $this->sale_price != null && $this->date_on_sale_from < Carbon::now() && $this->date_on_sale_to > Carbon::now()) ? true : false,
        );
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class);
    }
}
