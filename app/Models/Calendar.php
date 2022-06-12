<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_start',
        'date_end',
        'title',
        'duration',
        'description',
        'created_at',
        'updated_at'
    ];

    public function scopeBefore3hours($q)
    {
        return $q->where('date_start','<',Carbon::now()->addHours(3));
    }
}
