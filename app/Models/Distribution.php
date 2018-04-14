<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribution extends Model
{
    protected $fillable = [
        'project_distribution_id',
        'position',
        'user_id',
        'remark',
        'bonus',
        'formula'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function projectDistribution()
    {
        return $this->belongsTo(ProjectDistribution::class, 'project_distribution_id');
    }

    public function scopeOperator($query)
    {
        return $query->where('position', 7);
    }
}
