<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $guarded = ['id'];
    protected $fillable = array('name', 'label', 'description');

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function inRole($role)
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }
        return !!$role->intersect($this->roles)->count();
    }
}
///return $this->roles()->where('name', $role)->exists();
///coding is fun

