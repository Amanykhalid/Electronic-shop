<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\VendorRequest;
use App\Models\MainCategory;
use App\Models\Vendors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\VendorCreated;
use App\Models\main_categories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;



class VendorsController extends Controller
{
    //Show All Vendors
    public function index()
    {
        $vendors = Vendors::selection()->paginate(PAGINATION_COUNT);
        return view('admin.vendors.index', compact('vendors'));
    }

    //Show Create Vendor Pege
    public function createvendors()
    {
        $categories = main_categories::where('translation_of', 0)->active()->get();
        return view('admin.vendors.create', compact('categories'));
    }

    // Store New Vendor 
   public function storevendors(VendorRequest $request)
   {
    try {

        if (!$request->has('active'))
            $request->request->add(['active' => 0]);
        else
            $request->request->add(['active' => 1]);

        $filePath = "";
        if ($request->has('logo')) {
            $filePath = uploadImage('vendors', $request->logo);
        }

        $vendor = Vendors::create([
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'active' => $request->active,
            'address' => $request->address,
            'logo' => $filePath,
            'password' => $request->password,
            'category_id' => $request->category_id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);

        Notification::send($vendor, new VendorCreated($vendor));

        return redirect()->route('admin.vendors')->with(['success' => 'تم الحفظ بنجاح']);

    } catch (\Exception $ex) {
        return $ex;
        return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);

    }

   }

    //Show Edit Vendor Page 
    public function editvendors($vendorId)
    {
        try
        {
            $vendor=Vendors::selection()->find($vendorId);
            $categories = main_categories::where('translation_of', 0)->active()->get();

            if(!$vendor)
            {
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود']);
            }
            return view('admin.vendors.edit', compact('vendor','categories'));

        }
        catch(\Exception $ex)
        {
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    // Edit vendors
    public function updatevendors($id,VendorRequest $request)
    {
        try {

            $vendor = Vendors::Selection()->find($id);
            if (!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوفا ']);
            DB::beginTransaction();
            //photo
            if ($request->has('logo') ) {
                 $filePath = uploadImage('vendors', $request->logo);
                Vendors::where('id', $id)
                    ->update([
                        'logo' => $filePath,
                    ]);
            }
            if (!$request->has('active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);

             $data = $request->except('_token', 'id', 'logo', 'password');


            if ($request->has('password') && !is_null($request->password)) {

                $data['password'] = $request->password;
            }

            Vendors::where('id', $id)
                ->update(
                    $data
                );

            DB::commit();
            return redirect()->route('admin.vendors')->with(['success' => 'تم التحديث بنجاح']);
        } catch (\Exception $exception) {
            return $exception;
            DB::rollback();
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    // change Vendor Status
    public function changeStatus($id)
    {
        try {
            $vendor = Vendors::find($id);
            if (!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود ']);

           $status =  $vendor->active  == 0 ? 1 : 0;

           $vendor->update(['active' =>$status ]);

            return redirect()->route('admin.vendors')->with(['success' => ' تم تغيير الحالة بنجاح ']);

        } catch (\Exception $ex) {
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }

    // Delete Vendor 
    public function deletevendors($id)
    {
        try {
            $vendor = Vendors::find($id);
            if (!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود ']);

            $image = Str::after($vendor->logo, 'assets/');
            $image = base_path('assets/'. $image);
            unlink($image); //delete from folder
            $vendor->delete();
            return redirect()->route('admin.vendors')->with(['success' => 'تم حذف المتجر بنجاح']);

        } catch (\Exception $ex) {
            return $ex;
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }


}