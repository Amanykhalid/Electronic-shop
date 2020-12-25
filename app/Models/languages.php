<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class languages extends Model
{
    use HasFactory;

    protected $table = 'languages';

    protected $fillable = [
        'abbr', 'locale','name','diraction','active','created_at','updated_at',
    ];

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function  scopeSelection($query)
    {
        return $query -> select('id','abbr', 'name', 'diraction', 'active');
    }


    public function getActive()
    {
      return   $this -> active == 1 ? 'مفعل'  : 'غير مفعل';
    }

}
