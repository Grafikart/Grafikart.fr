<?php

namespace App\Domains\Premium\Models;

use App\Domains\Premium\Factory\PlanFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    /** @use HasFactory<PlanFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'duration',
        'stripe_id',
    ];

    protected static function newFactory(): PlanFactory
    {
        return PlanFactory::new();
    }
}
