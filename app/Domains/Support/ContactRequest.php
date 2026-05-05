<?php

namespace App\Domains\Support;

use App\Domains\Support\Factory\ContactRequestFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    /** @use HasFactory<ContactRequestFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'message',
        'ip',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
        ];
    }

    protected static function newFactory(): ContactRequestFactory
    {
        return ContactRequestFactory::new();
    }
}
