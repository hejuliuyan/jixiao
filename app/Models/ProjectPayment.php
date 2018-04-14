<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectPayment extends Model
{
    protected $touches = ['project'];

    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function payment()
    {
        return $this->hasMany(Payment::class);
    }

    public function getTenderTotalAttribute()
    {
        return $this->payment()
            ->where('type', 1)
            ->sum('subtotal');
    }

    public function getConsultTotalAttribute()
    {
        return $this->payment()
            ->where('type', 2)
            ->sum('subtotal');
    }
}
