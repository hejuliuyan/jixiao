<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'project_payment_id',
        'user_id',
        'type',
        'detail',
        'subtotal'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function projectPayment()
    {
        return $this->belongsTo(ProjectPayment::class, 'project_payment_id');
    }

    public function getAccountsAttribute()
    {
        return json_decode($this->detail);
    }
}
