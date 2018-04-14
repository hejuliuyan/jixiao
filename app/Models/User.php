<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

class User extends Authenticatable
{
    use EntrustUserTrait {
        restore as private restoreEntrust;
        EntrustUserTrait::can as may;
    }
    use SoftDeletes { restore as private restoreSoftDelete; }
    protected $dates = ['deleted_at'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','division','is_banned'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token'
    ];

    /**
     * For EntrustUserTrait and SoftDeletes conflict
     */
    public function restore()
    {
        $this->restoreEntrust();
        $this->restoreSoftDelete();
    }

    public function role()
    {
        return $this->roles();
    }

    public function scopeRelate($query, $value)
    {
        return $query->where('division', $value);
    }

    public function scopeMembers($query)
    {
        return $query->whereIn('division', [1,2,3,4,5,6,7]);
    }

    public function getFullNameAttribute()
    {
        return $this->name.'['.config('admin.sites.division')[$this->division].']';
    }
}
