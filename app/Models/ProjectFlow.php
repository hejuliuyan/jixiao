<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectFlow extends Model
{
    /**
     * 所有的关联将会被连动
     */
    protected $touches = ['project'];

    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function flow()
    {
        return $this->hasMany(Flow::class);
    }
}
