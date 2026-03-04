<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'web_page_url',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'phone',
        'email',
        'logo_path',
    ];

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}