<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\MainCategoryRequest;
use App\Models\main_categories;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



class MainCategory extends Controller
{
    
    //Show All Category 
    public function index()
    {
        $default_lang = get_default_lang();
        $categories = main_categories::where('translation_lang', $default_lang)
            ->selection()
            ->get();
        return view('admin/maincategories/index',compact('categories'));
    }

    //Show Add new Category Page
    public function createCategory()
    {
        return view('admin/maincategories/create');
    }

    //Add new Category
    public function storeCategory(MainCategoryRequest $request)
    {
        try {
            $main_categories = collect($request->category);

            $filter = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] == get_default_lang();
            });

            $default_category = array_values($filter->all()) [0];

            $filePath = "";
            if ($request->has('photo')) {
                $filePath = uploadImage('maincategories', $request->photo);
            }

            DB::beginTransaction();

            $default_category_id = main_categories::insertGetId([
                'translation_lang' => $default_category['abbr'],
                'translation_of' => 0,
                'name' => $default_category['name'],
                'slug' => $default_category['name'],
                'photo' => $filePath
            ]);

            $categories = $main_categories->filter(function ($value, $key) {
                return $value['abbr'] != get_default_lang();
            });


            if (isset($categories) && $categories->count()) {
                $categories_arr = [];
                foreach ($categories as $category) {
                    $categories_arr[] = [
                        'translation_lang' => $category['abbr'],
                        'translation_of' => $default_category_id,
                        'name' => $category['name'],
                        'slug' => $category['name'],
                        'photo' => $filePath
                    ];
                }
                main_categories::insert($categories_arr);
            }

            DB::commit();

            return redirect()->route('admin.maincategories')->with(['success' => 'تم الحفظ بنجاح']);

        } catch (\Exception $ex) {
            DB::rollback();
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }

    }

    //Show Edit Category Page
    public function editCategory($mainCat_id)
    {
         //get specific categories and its translations
         $mainCategory = main_categories::with('categories')
         ->selection()
         ->find($mainCat_id);

     if (!$mainCategory)
         return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود ']);

         return view('admin.maincategories.edit', compact('mainCategory'));

    }

    // Edit Category 
    public function updateCategory(MainCategoryRequest $request,$mainCat_id)
    {
        try {
            $main_category = main_categories::find($mainCat_id);

            if (!$main_category)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود ']);

            // update date

            $category = array_values($request->category) [0];

            if (!$request->has('category.0.active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);


                main_categories::where('id', $mainCat_id)
                ->update([
                    'name' => $category['name'],
                    'active' => $request->active,
                ]);

            // save image
            if ($request->has('photo')) {
                $filePath = uploadImage('maincategories', $request->photo);
                main_categories::where('id', $mainCat_id)
                    ->update([
                        'photo' => $filePath,
                    ]);
            }
            return redirect()->route('admin.maincategories')->with(['success' => 'تم ألتحديث بنجاح']);
        } catch (\Exception $ex) {

            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }



    }

    // Change status Category
    public function changeStatus($id)
    {
        try {
            $maincategory = main_categories::find($id);
            if (!$maincategory)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود ']);

           $status =  $maincategory->active  == 0 ? 1 : 0;

           $maincategory->update(['active' =>$status ]);

            return redirect()->route('admin.maincategories')->with(['success' => ' تم تغيير الحالة بنجاح ']);

        } catch (\Exception $ex) {
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    } 

    // Delete Category 
    public function deleteCategory($id)
    {

        try {
            $maincategory = main_categories::find($id);
            if (!$maincategory)
                return redirect()->route('admin.maincategories')->with(['error' => 'هذا القسم غير موجود ']);

            $vendors = $maincategory->vendors();
            if (isset($vendors) && $vendors->count() > 0) {
                return redirect()->route('admin.maincategories')->with(['error' => 'لأ يمكن حذف هذا القسم  ']);
            }

            $image = Str::after($maincategory->photo, 'assets/');
            $image = base_path('assets/' . $image);
            unlink($image); //delete from folder

            $maincategory->delete();
            return redirect()->route('admin.maincategories')->with(['success' => 'تم حذف القسم بنجاح']);

        } catch (\Exception $ex) {
            return redirect()->route('admin.maincategories')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }
}
