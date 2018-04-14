<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Nicolaslopezj\Searchable\SearchableTrait;
use Auth;

class Project extends Model
{
    use SearchableTrait;

    /**
     * Searchable rules.
     */
    protected $searchable = [
        'columns' => [
            'num' => 10,
            'name' => 8
        ],
    ];

    protected $guarded = ['id'];

    public function projectFlow()
    {
        return $this->hasMany(ProjectFlow::class);
    }

    public function projectPayment()
    {
        return $this->hasMany(ProjectPayment::class);
    }

    public function projectDistribution()
    {
        return $this->hasMany(ProjectDistribution::class);
    }

    public function scopeDate($query, $period)
    {
        return $query->whereBetween('updated_at', $period);
    }

    public function scopeMember($query, $user)
    {
        return $query->whereHas('projectDistribution', function ($query_f) use ($user) {
                    $query_f->whereHas('distribution', function ($query_s) use ($user) {
                        $query_s->where('user_id', $user->id);
                    });
                });
    }

    public function scopeRun($query)
    {
        return  $query->where('state', '>', 0);
    }

    /**
     * 是否为全过程项目类别
     */
    public function getIsWholeAttribute()
    {
        $categoryArr = explode(',', $this->category);
        return in_array(10, $categoryArr);
    }

    /**
     * 最后一次流转次数
     */
    public function getMaxNumAttribute()
    {
        return $this->projectFlow()->max('num');
    }

    public function getIncomeTotalAttribute()
    {
        return $this->projectPayment()->where('status', 1)->sum('total');
    }

    public function getDistributionTotalAttribute()
    {
        return $this->projectDistribution()->where('status', 1)->sum('total');
    }
}
