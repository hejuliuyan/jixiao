<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectDistribution extends Model
{
    protected $touches = ['project'];

    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function distribution()
    {
        return $this->hasMany(Distribution::class);
    }

    public function getHasOperatorAttribute()
    {
        return $this->distribution()->where('position', 7)->count();
    }
}
