<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Vendors extends Model
{
    use Notifiable;

    protected $table = 'vendors';

    protected $fillable = [
        'name', 'mobile', 'password', 'address', 'email', 'logo','latitude','longitude', 'category_id', 'active', 'created_at', 'updated_at'
    ];

    protected $hidden = ['category_id', 'password'];


    public function scopeActive($query)
    {

        return $query->where('active', 1);
    }

    public function getLogoAttribute($val)
    {
        return ($val !== null) ? asset('assets/' . $val) : "";

    }


    public function scopeSelection($query)
    {
        return $query->select('id', 'category_id', 'active', 'name', 'address', 'email','latitude','longitude', 'logo', 'mobile');
    }


    public function category()
    {

        return $this->belongsTo('App\Models\main_categories', 'category_id', 'id');
    }

    public function getActive()
    {
        return $this->active == 1 ? 'مفعل' : 'غير مفعل';

    }


    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }
}