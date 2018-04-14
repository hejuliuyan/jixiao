<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Flow extends Model
{
    protected $touches = ['projectFlow'];

    protected $fillable = [
        'project_flow_id',
        'user_id',
        'result',
        'remark'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function projectFlow()
    {
        return $this->belongsTo(ProjectFlow::class, 'project_flow_id');
    }

    public function scopeFiled($query)
    {
        return $query->where('result', 3)->whereNotNull('user_id');
    }
}
