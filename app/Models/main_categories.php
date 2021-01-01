<?php

namespace App\Models;

use App\Observers\main_categoriesObserve;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class main_categories extends Model
{
    use HasFactory;

    protected $table = 'main_categories';

    protected $fillable = [
        'translation_lang', 'translation_of', 'name', 'slug', 'photo', 'active', 'created_at', 'updated_at'
    ];

    protected static function boot()
    {
        parent::boot();
        main_categories::observe(main_categoriesObserve::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeSelection($query)
    {

        return $query->select('id', 'translation_lang', 'name', 'slug', 'photo', 'active', 'translation_of');
    }

    public function getPhotoAttribute($val)
    {
        return ($val !== null) ? asset('assets/' . $val) : "";

    }

    public function getActive()
    {
        return $this->active == 1 ? 'مفعل' : 'غير مفعل';

    }

    public function scopeDefaultCategory($query){
        return  $query ->where('translation_of',0);
    }


      // get all translation categories
    public function categories()
    {
        return $this->hasMany(self::class, 'translation_of');
    }


    public  function subCategories(){
        return $this ->hasMany(SubCategory::class,'category_id','id');
    }

    public function vendors(){

        return $this ->hasMany('App\Models\Vendors','category_id','id');
    }

  

}
