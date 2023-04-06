<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Episode extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'episodes';
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
}
